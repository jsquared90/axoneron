<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Pre Register User - Version 1.0 ************************
 *
 * This script allows you to pre register user, aka add user.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The user data is then checked to make sure all of the required fields are entered.
 *
 * 4. If everything is entered correctly, we then call the function preregisterUser by passing in the user variable, and then assigning the result to a new variable, result.
 *
 * 5. If successful it returns a -1 and continues on.
 *
 * 6. The email that was entered during the creation process is then assigned to a new variable, email.
 *
 * 7. We then call the function getUserByEmail by passing in the email variable. This is then assigned to a new variable, newUser.
 *
 * 8. If successful, we echo out newUser.
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
        isset($_POST['newUserEmail']) &&
        isset($_POST['newUserTitle']) &&
        isset($_POST['newUserLevel']) &&
        isset($_POST['newUserRole']) &&
        isset($_POST['newUserCC']))
    {
        $result = preregisterUser($user, $connection);
        $queryError = queryError($result, POST_ADD_USER, 0);
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