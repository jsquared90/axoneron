<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

/*
 * **************** Get Conversation - Version 1.4 ************************
 *
 * The script displays a conversation between two users or all messages in a group.
 *
 * 1. The script first checks to make sure a valid user is authenticated.
 *
 * 2. The script then checks to make sure recipient isset and assigns it to the variable recipient.
 *
 * 3. We then declare the variable group and assign it to 0 for the time being.
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
        $conversation = getConversation($user, $recipient, $recipientType, $connection);
        if ($conversation)
        {
            $conversationPackage = array(
            'conversation' => $conversation,
            'messageGroup' => 0
            );
            echo packageDataForUnity($format, $conversationPackage, "conversationPackage");
        }
        else
        {
            echo packageDataForUnity($format, $conversation, null);
        }
    }
    else if ($group)
    {
        $identifier = isPresetGroup($group) ? $group : $group['id'];
        $messageGroup = getMessageGroupByID($identifier, $user, $connection);
        $conversation = getConversation($user, $messageGroup, $recipientType, $connection);
        if ($conversation)
        {
            $conversationPackage = array(
            'conversation' => $conversation,
            'messageGroup' => $messageGroup
            );
            echo packageDataForUnity($format, $conversationPackage, "conversationPackage");
        }
        else
        {
            echo packageDataForUnity($format, $conversation, null);
        }
    }
    else
    {
        sendIncompleteFormDataError($format);
    }
}