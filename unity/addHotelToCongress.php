<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Hotel - Version 1.1 ************************
 *
 * This script adds a hotel to the database provided all of the pertinent information is given.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hotel.
 *
 * 3. The script first checks to make sure congress and hotelList are set.
 *
 * 4. If congressID isset, we get the congress by it's ID from the database.
 *
 * 5. The script then checks to make sure all of the pertinent information is given in the function isHotelDataSet.
 *
 * 6. If successful, a new hotel is added to the database.
 *
 * 7. The newly created hotel is then echoed out by the function, getHotelByNameAndLocation with the initial provided information.
 *
 * 8. If a hotel is already entered into the database, but not associated with a congress, it is then available in a drop down menu for selection.
 *
 * 9. After selected to be added to a current congress, a confirmation message is displayed on screen.
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
            $hotelID = isset($_POST['hotelList']) ? $_POST['hotelList'] : null;
            if ($hotelID != null)
            {
                $hotel = getHotelById($hotelID, $connection);
                if ($hotel)
                {
                    $result = addHotelToCongress($user, $connection);
                    $queryError = queryError($result, POST_ADD_HOTEL_TO_CONGRESS, 0);
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
                    echo packageTypeErrorForUnity($format, "The specified hotel was unable to be found.", 2);
                }
            }
            elseif (isHotelDataSet(false))
            {
                $result = addHotelToCongress($user, $connection);
                $queryError = queryError($result, POST_ADD_HOTEL_TO_CONGRESS, 0);
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