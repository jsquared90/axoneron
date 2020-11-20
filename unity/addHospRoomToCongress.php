<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Hospitality Room - Version 1.1 ************************
 *
 * This script adds a hospitality room to the database provided all of the pertinent information is given.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hospitality room.
 *
 * 3. The script then checks to make sure all of the required fields are filled out.
 *
 * 4. In the field checking process, it also asks for how many rooms will be needed. The script adjusts for multiple rooms.
 *
 * 5. If everything is entered correctly, a hospitality room is then created and added to the congress.
 *
 * 6. The congressID as well as the hospitality room name are declared and then passed into the function: getHospitalityRoomByName.
 *
 * 7. If successful, the new hospitality room is then echoed out.
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
        $congress = validateCongress($connection, $format);
        if ($congress)
        {
            if (hospDataIsSet())
            {
                $result = addHospitalityRoomToCongress($user, $connection);
                $queryError = queryError($result, POST_ADD_HOSP_ROOM_TO_CONGRESS, 0);
                if ($queryError["code"] >= 0)
                {
                    echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
                }
                else
                {
                    echo packageDataForUnity($format, $result['record'], "userRecord");
                }
            }
            else
            {
                sendIncompleteFormDataError($format);
            }
        }
    }
}

/*
 * Sub Functions
 */

function hospDataIsSet()
{
    $valid = 0;
    if (isset($_POST['congressID']) &&
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