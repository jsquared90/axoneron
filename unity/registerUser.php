<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Register User - Version 1.0 ************************
 *
 * This script allows the user to finish registering to the database.
 *
 * 1. We first check to make sure that the email address is entered, and valid in the database. The id is also associated with the entered email.
 *
 * 2. We then assign $userID to the id that was looked up.
 *
 * 3. We also assign $email to the email parameter.
 *
 * 4. The function getUserById is then called with the userID variable passed through it. It is then assigned to the new variable, user.
 *
 * 5. The database then displays a form that allows the user to finish entering in the required fields.
 *
 * 6. After the user finishes entering, they then hit submit which writes to the database.
 *
 * 7. If successful, the entry is then echoed out.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$user = 0;

if (isset($_POST['id']) &&
    isset($_POST['email']))
{
    $userID = $_POST['id'];
    $email = $_POST['email'];
    $user = getUserById($userID, $connection);
    if ($user)
    {
        if ($user['email'] == $email)
        {
            if ($user['password'] == "")
            {
                if (isset($_POST['first']) &&
                    isset($_POST['last']) &&
                    isset($_POST['pw1']) &&
                    isset($_POST['pw2']) &&
                    isset($_POST['phone']) &&
                    isset($_POST['title']) &&
                    isset($_POST['role']) &&
                    isset($_POST[POST_REGISTER_USER]))
                {
                    $result = registerUser($user, $connection);
                    $queryError = queryError($result, POST_REGISTER_USER, 0);
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
            else
            {
                echo packageTypeErrorForUnity($format, "The email address is already associated with a registered user.", 5);
            }
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Unauthorized access.", 4);
        }
    }
    else
    {
        echo packageTypeErrorForUnity($format, "There was an error trying to retrieve the user.", 3);
    }
}
else
{
    sendIncompleteFormDataError($format);
}