<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

$user = validateUserForUnity($connection, $format);
if ($user)
{
    if (checkUserLevel($user, $format) > 1)
    {
        if (isset($_POST['userIDs']))
        {
            $userIDs = explode(",", $_POST['userIDs']);
            $result = removeUsers($userIDs, $user, $connection);
            $queryError = queryError($result, POST_REMOVE_USERS, 0);
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

