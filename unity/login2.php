<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Login Part 2 - Version 1.0 ************************
 *
 * This script is used for step two in the registration process for a user
 * who has been pre-registered. Here are the steps within:
 *
 * 1. The user provides an email address.
 *
 * 2. The script then checks to make sure the password is entered.
 *
 * 3. The server/installation returns whether the user is valid or not.
 *
 * 4. If both email and password are valid, the server then returns the complete details of the user record from the database.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

$id = isset($_POST['user']) ? $_POST['user'] : null;
if (isset($id))
{
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    if (isset($password))
    {
        $user = getUserByFullValidation($id, $password, $connection);
        if ($user)
        {
            
            echo packageDataForUnity($format, $user, "user");
        }
        else
        {
            echo packageTypeErrorForUnity($format, "Unauthorized access.", 3);
        }
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}
else
{
    sendIncompleteFormDataError($format);
}
