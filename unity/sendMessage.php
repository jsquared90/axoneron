<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Send Message - Version 1.0 ************************
 *
 * The script sends a message between two users or all messages in a group.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script first checks to make sure the parameter recipient isset. This is then assigned to a new variable, $recipient.
 *    The script also checks to see if the group isset. This is then assigned to a new variable, $group.
 * 
 * 3. We then check to make sure the parameter notePadData isset. This is done for either condition, $recipient or $group.
 *
 * 3. We then call the function sendMessage. $user and $connection are passed through. This is assigned to $addMessage.
 * 
 * 4. We then call the function getConversation. $user, $recipient, $recipientType, and $connection are passed through.
 *    This is assigned to $conversation.
 * 
 * 5. Finally the respective $conversations are echoed out.
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
    $recipient = isset($_POST['recipient']) ? getUserById($_POST['recipient'], $connection) : null;
    $group = 0;
    if (isset($_POST['group']))
    {
        $group = isPresetGroup($_POST['group']) ? $_POST['group'] : getMessageGroupByID($_POST['group'], $user, $connection);
    }
    $recipientType = $recipient ? "private" : "group";
    if ($recipient)
    {
        if (isset($_POST['notePadData']))
        {
            $result = sendMessage($user, $connection);
            $queryError = queryError($result, POST_SEND_MESSAGE, 0);
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
    else if ($group)
    {
        $identifier = isPresetGroup($group) ? $group : $group['id'];
        $messageGroup = getMessageGroupByID($identifier, $user, $connection);
        if (isset($_POST['notePadData']))
        {
            $result = sendMessage($user, $connection);
            $queryError = queryError($result, POST_SEND_MESSAGE, 0);
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
        sendIncompleteFormDataError($format);
    }
}