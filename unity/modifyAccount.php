<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Modify Account - Version 1.1 ************************
 *
 * This script allows you to modify the account of an existing user.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The user data is then checked to make sure all of the required fields are entered.
 *
 * 4. If everything is entered correctly, we then call the function modifyAccount by passing in the user variable, and then assigning the result to a new variable, result.
 *
 * 5. If successful it returns a -1 and continues on.
 *
 * 6. Then the ID that's associated with the user account is then assigned to a new variable id.
 *
 * 7. We then pass the variable, id, into the function getUserById and assign it to modifiedUser.
 *
 * 8. If successful, we echo out modifiedUser.
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
    if (isset($_POST['newUserFirst']) &&
        isset($_POST['newUserLast']) &&
        isset($_POST['newUserPhone']) &&
        isset($_POST['newUserTitle']) &&
        isset($_POST['newUserRole']) &&
        isset($_POST['id']))
    {
        $result = modifyAccount($user, $connection);
        $queryError = queryError($result, POST_MODIFY_ACCOUNT, 0);
        if ($queryError["code"] >= 0)
        {
            echo packageTypeErrorForUnity($format, $queryError["message"], $queryError["code"]);
        }
        else
        {
            echo packageDataForUnity($format, $result['user'], "user");
        }
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}