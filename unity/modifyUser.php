<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify User - Version 1.0 ************************
 *
 * This script allows an admin to modify a user's account.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. A user level needs to be over 1 in order to modify a user account.
 *
 * 3. The script then checks to make sure POST_MODIFY_USERS isset.
 * 
 * 4. The script then checks to make sure all of the required fields are filled out.
 *
 * 5. We then check to make sure the parameter userID isset. This is assigned to $userID.
 *
 * 6. The function modifyUser is then called. $userID and $connection are passed through.
 *    This is assigned to $modifyUser.
 *
 * 7. We then call the function getUserById. We pass through $userID and $connection. This is assigned to $modifiedUser.
 *
 * 8. If successful, the $modifiedUser is then echoed out.
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
        if (isset($_POST['newUserFirst']) &&
            isset($_POST['newUserLast']) &&
            isset($_POST['newUserPhone']) &&
            isset($_POST['newUserTitle']) &&
            isset($_POST['newUserLevel']) &&
            isset($_POST['newUserRole']) &&
            isset($_POST['userID']))
        {
            $result = modifyUser($user, $connection);
            $queryError = queryError($result, POST_MODIFY_USER, 0);
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
