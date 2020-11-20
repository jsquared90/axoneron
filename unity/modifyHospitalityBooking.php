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
 * This script modifies a hospitality room booking request for a given hospitality room.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script then checks to make sure the parameter congressID isset.
 *    This is assigned to a new variable, $congressID
 *
 * 3. We then call the function getCongressById by passing in the $congressID and $connection.
 *    This is assigned to a new variable, $congress.
 *
 * 4. We then check to make sure the parameter roomID isset. This is assigned to a new variable, $roomID.
 * 
 * 5. We then check to make sure the parameter bookingID isset. This is assigned to a new variable, $bookingID.
 *
 * 6. We then check to make sure the following parameters are set: bookingName, dateInput, startTime, endTime, and openEnd.
 *
 * 7. The function modifyHospitalityRoomBooking is then called. We pass in the $user and the $connection.
 *    This is assigned to a new variable, $modifyHospRoomBook.
 * 
 * 8. Finally if successful, $modifyHospRoomBook is echoed out.
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
    $congress = validateCongress($connection, $format);
    if ($congress)
    {
        $bookingID = isset($_POST['bookingID']) ? $_POST['bookingID'] : null;
        if ($bookingID)
        {
            if (isset($_POST['bookingName']) &&
                isset($_POST['dateInput']) &&
                isset($_POST['startTime']) &&
                isset($_POST['endTime']) &&
                isset($_POST['openEnd']))
            {
                $startTime = ($_POST['startTime']);
                $endTime = ($_POST['endTime']);
                if (strtotime($startTime) < strtotime($endTime))
                {
                    $result = modifyHospitalityRoomBooking($user, $connection);
                    $queryError = queryError($result, POST_MODIFY_HOSP_BOOKING);
                    if ($queryError["code"] >= 0)
                    {
                        echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
                    }
                    else
                    {
                        echo packageDataForUnity($format, $result["record"], "userRecord");
                    }
                }
                else
                {
                    echo packageTypeErrorForUnity($format, "Invalid hospitality room time values entered.", 3);
                }
            }
            else
            {
                sendIncompleteFormDataError($format);
            }
        }
        else
        {
            sendIncompleteFormDataError($format);
        }
    }
}
