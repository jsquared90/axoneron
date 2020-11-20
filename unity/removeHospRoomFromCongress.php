<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Hospitality Room - Version 1.0 ************************
 *
 * This script removes a hospitality room from the database for a specified congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 2 in order to remove a hospitality room.
 *
 * 3. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 * 
 * 5. We then check to make sure the parameter hospRoomID is set. This is assigned to a new variable, $hospRoom.
 *
 * 6. We then call the function removeHospitalityRoomFromCongress. We pass in the variables $user and $connection. This is assigned to a new variable, $removeHospRoom.
 *
 * 6. If the 'code' of $removeHospRoom is less than 1, we then call the function getAllHospitalityRoomsForCongress. We pass in the variables $congress and $connection.
 *    This is assigned to a new variable, $hospRooms.
 *
 * 7. If successful, the remaining hospitality rooms are echoed out.
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
            $hospRoom = isset($_POST['hospRoomID']) ? $_POST['hospRoomID'] : null;
            if ($hospRoom)
            {
                $result = removeHospitalityRoomFromCongress($user, $connection);
                $queryError = queryError($result, POST_REMOVE_HOSP_ROOM_FROM_CONGRESS, 0);
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
