<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
 * **************** Login Part 1 - version 1.0 ************************
 * 
 * This script is used for step one in the registration process for a user
 * who has been pre-registered. Here are the steps within:
 * 
 * 1. The user provides an email address
 * 
 * 2. The server/installation validates whether that user has been pre-registered,
 * using the provided email address
 * 
 * 3. The server/installation returns whether the user is valid or not
 * 
 * 4. 3rd party application should use that validation to determine whether to 
 * go to step 2 of the login process or report a failure/error
 * 
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 * 
 */

$email = isset($_POST['email']) ? $_POST['email'] : null;
if (isset($email))
{
    $user = getUserByEmail($email, $connection);
    if ($user)
    {
        echo packageDataForUnity($format, $user, "user");
    }
    else
    {
        echo packageTypeErrorForUnity($format, "Unauthorized access.", 2);
    }
}
else
{
    sendIncompleteFormDataError($format);
}
