<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Hospitality Room - Version 1.0 ************************
 *
 * This script modifies a hospitality room in a given congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hospitality room.
 *
 * 3. We call the function modifyHospitalityRoom. We pass through $user and $connection. This is assigned to $modifyHospitalityRoom.
 *
 * 4. We assign the parameter congressID to $congressID. And the parameter newHospRoomName to $name.
 *
 * 5. We then call the function getHospitalityRoomByName. We pass through $congressID, $name, and $connection.
 *    This is assigned to $result.
 *
 * 6. If successful, the modified hospitality room is then echoed out.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$user = validateUserForUnity($connection, $format);
if ($user)
{
    if (checkUserLevel($user, $format) > 1)
    {
        if (validateFormVariables())
        {
            $result = modifyHospitalityRoom($user, $connection);
            $queryError = queryError($result, POST_MODIFY_HOSP_ROOM);
            if ($queryError["code"] >= 0)
            {
                echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);

            }
            else
            {
                // this needs to be customized to include afftected bookings back to 3rd party app
                //echo packageDataForUnity($format, $result['record'], "userRecord");
                $affectedBookings = !$result['affectedBookings'] ? null : $result['affectedBookings'];
                
                $data = array(
                    "userRecord" => $result['record'],
                    "affectedBookings" => $affectedBookings
                );
                echo packageDataForUnity($format, $data, "hospRoomModificationData");
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}

/*
 * Sub Functions
 */

function validateFormVariables()
{
    $valid = 0;
    if (isset($_POST['congressID']) &&
        isset($_POST['hospRoomID']) &&
        isset($_POST['newHospRoomName']) &&
        isset($_POST['newHospRoomLocation']) &&
        isset($_POST['newHospRoomSize']) &&
        isset($_POST['numTimeSlots']))
    {
        $valid = 1;
        $numOfRooms = $_POST['numTimeSlots'];
        for ($i = 1 ; $i < ($numOfRooms + 1) ; $i++)
        {
            if(!isset($_POST['startDate' . $i]) ||
                !isset($_POST['startTime' . $i]) ||
                !isset($_POST['startMeridian' . $i]) ||
                !isset($_POST['endTime' . $i]) ||
                !isset($_POST['endMeridian' . $i]))
            {
                $valid = 0;
            }
        }
    }
    return $valid;
}