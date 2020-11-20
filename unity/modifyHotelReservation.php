<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Hotel Reservation - Version 1.0 ************************
 *
 * This script allows a user to modify a hotel reservation request.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 * 
 * 2. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 * 
 * 3. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 *
 * 4. We then check to make sure the following parameters are set: roomType, occupancy, openEnd, and POST_MODIFY_HOTEL_RESERVATION.
 *
 * 5. The function modifyHotelRequest is then called. We pass in the $user and the $connection. This is assigned to a new variable, $confirmation.
 * 
 * 6. Finally if successful, the $confirmation is echoed out.
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
        if (isset($_POST['roomType']) &&
            isset($_POST['occupancy']) &&
            isset($_POST['openEnd']))
        {
            $result = modifyHotelRequest($user, $connection);
            $queryError = queryError($result, POST_MODIFY_HOTEL_RESERVATION, 0);
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
