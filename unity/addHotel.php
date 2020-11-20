<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Add Hotel - Version 1.0 ************************
 *
 * This script adds a hotel to the database provided all of the pertinent information is given.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hotel.
 *
 * 3. The script then checks to make sure all of the required fields are filled out.
 * 
 * 4. We then check to make sure POST_ADD_HOTEL, which is attached to the submit button, has been pressed.
 * 
 * 5. The function addHotelToDatabase is then called. $user and $connection are passed through. This is assigned to $result.
 * 
 * 6. queryError is then called. $result, POST_ADD_HOTEL, and 0 (this delineates that the script is not part of the web build) are passed through. 
 *    This is assigned to queryError.
 * 
 * 7. If queryError returns a number greater or equal 0, the corresponding error messages are then echoed out.
 * 
 * 8. If queryError returns a number less than 0, the userRecord is then echoed out showing that the hotel addition was successful.
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
        if (isHotelDataSet(false))
        {
            $result = addHotelToDatabase($user, $connection);
            $queryError = queryError($result, POST_ADD_HOTEL, 0);
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