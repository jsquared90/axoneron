<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** View Requests - Version 1.1 ************************
 *
 * This script gets all requests.
 *
 * 1. The script first checks to make sure the user parameter isset, which is a number, and then assigns it to userID.
 *
 * 2. We then pass the userID variable into the getUserById function and assign that to a new variable, user.
 *
 * 3. If a user exists, we then check to make sure the user's level is greater than 1.
 *
 * 4. We then call the function getAllPendingRequests by passing through the user variable. This is assigned to a newly created variable, requests.
 *
 * 5. If successful, we echo out the result.
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
        $requests = getAllPendingRequests($user, $connection);
        if ($requests)
        {
            echo packageDataForUnity($format, $requests, "requests");
        }
        else
        {
            echo packageDataForUnity($format, null, "requests");
        }
    }
}