<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Remove Hotel - Version 1.0 ************************
 *
 * This script removes a hotel from the database for a specified congress.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 2 in order to remove a hotel.
 *
 * 3. The script then checks to make sure the parameter congressID isset. This is assigned to a new variable, $congressID
 *
 * 4. We then call the function getCongressById by passing in the $congressID and $connection. This is assigned to a new variable, $congress.
 * 
 * 5. We then check to make sure the parameter hotelID is set. This is assigned to a new variable, $hotel.
 *
 * 6. We then call the function removeHotelFromCongress. We pass in the variables $user and $connection. This is assigned to a new variable, $removeHotel.
 *
 * 7. We then call the function getHotelsWithCongress. We pass in the variables $congress and $connection This is assigned to a new variable, $hotels.
 *
 * 8. If successful, the remaining hotel(s) are echoed out.
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
            $hotel = isset($_POST['hotelID']) ? $_POST['hotelID'] : null;
            if ($hotel)
            {
                $result = removeHotelFromCongress($user, $connection);
                $queryError = queryError($result, POST_REMOVE_HOTEL_FROM_CONGRESS, 0);
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
