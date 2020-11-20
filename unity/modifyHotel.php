<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Hotel Room - Version 1.3 ************************
 *
 * This script allows an admin to modify a hotel
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to add a hospitality room.
 *
 * 3. We then check to make sure all of the required fields are filled out.
 * 
 * 4. POST_MODIFY_HOTEL is then checked to make sure it was submitted.
 * 
 * 5. modifyHotelDetail is then called. $user and $connection are passed through. $result is assigned.
 * 
 * 6. queryError is then called. $result, POST_MODIFY_HOTEL, and 0 are passed through. $queryError is assigned.
 * 
 * 7. If the value returned is equal to or greater than 0, the corresponding error message is echoed out.
 * 
 * 8. Otherwise the record being added to the user is returned.
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
        if (isHotelDataSet(true))
        {
            $result = modifyHotelDetail($user, $connection);
            $queryError = queryError($result, POST_MODIFY_HOTEL, 0);
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