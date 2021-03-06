<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Confirm Hotel Room Request - Version 1.0 ************************
 *
 * This script confirms a hotel room request from a user.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script then checks to make sure the user's level is greater than 1.
 *
 * 3. We first call the function getAllPendingRequests. We pass through the variables $user and $connection.
 *    This is assigned to a new variable, $requests.
 * 
 * 4. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 * 
 * 5. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 *
 * 6. We then check to make sure the following parameters are set: checkInDate, checkOutDate, roomType, occupancy, hotelList, confirmationNumber, recordID, openEnd, and POST_CONFIRM_HOTEL.
 *
 * 7. The function confirmHotelRequest is then called. We pass in the $user and the $connection. This is assigned to a new variable, $confirmation.
 * 
 * 8. Finally if successful, the $confirmation is echoed out.
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
            if (isset($_POST['checkInDate']) &&
                isset($_POST['checkOutDate']) &&
                isset($_POST['roomType']) &&
                isset($_POST['occupancy']) &&
                isset($_POST['hotelList']) &&
                isset($_POST['confirmationNumber']) &&
                isset($_POST['recordID']) &&
                isset($_POST['openEnd']))
            {
                $checkInDate = ($_POST['checkInDate']);
                $checkOutDate = ($_POST['checkOutDate']);
                if (strtotime($checkInDate) < strtotime($checkOutDate))
                {
                    $result = confirmHotelRequest($user, $connection);
                    $queryError = queryError($result, POST_CONFIRM_HOTEL, 0);
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
                    echo packageTypeErrorForUnity($format, "Invalid hotel date range values entered.", 2);
                }
            }
            else
            {
                sendIncompleteFormDataError($format);
            }
        }
    }
}
