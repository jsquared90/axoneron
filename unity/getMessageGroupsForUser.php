<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

/*
* **************** SUB-FUNCTIONS ************************
*/

$user = validateUserForUnity($connection, $format);
if ($user)
{
    
    $messageGroups = getMyMessageGroups($user, $connection);
    if ($messageGroups)
    {
        echo packageDataForUnity($format, $messageGroups, "messageGroups");
    }
    else
    {
        echo packageDataForUnity($format, null, "messageGroups");
    }
}