<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Users - Version 1.0 ************************
 *
 * The script displays all users.
 *
 * 1. We assign a new variable, users, to the function getAllUsers.
 *
 * 2. We then echo out the result.
 *
 * NOTES: all data is returned in JSON format. Other formats (eg. xml) may be supported
 * at a later date, but JSON will be the default. When other formats are supported in the future,
 * format can be specified by including a POST variable definition for "format."
 * If at any point in time a field, step, process is entered/done incorrectly a corresponding error message will display
 * so that the user can figure out how to fix it.
 *
 */

/*
 * 
 * TODO : Need to do authentication for callers for security purposes
 * 
 */
$user = validateUserForUnity($connection, $format);
if ($user)
{
    $users = getAllUsers($connection);
    if ($users)
    {
        echo packageDataForUnity($format, $users, "users");
    }
    else
    {
        echo packageTypeErrorForUnity($format, "There are currently no users in the database.", 1);
    }
}
