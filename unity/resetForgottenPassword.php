<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
* **************** MEAT & POTATOES WORK BELOW ************************
*/

$user = 0;
if (isset($_POST['id']) &&
    isset($_POST['newPassword']))
{
    $userID = $_POST['id'];
    $user = getUserById($userID, $connection);
    if ($user)
    {
        $result = resetPassword($user, $connection);
        $queryError = queryError($result, POST_RESET_FORGOTTEN_PASSWORD, 0);
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
        echo packageTypeErrorForUnity($format, "There was an error trying to retrieve the user.", 2);
    }
}
else
{
    sendIncompleteFormDataError($format);
}
