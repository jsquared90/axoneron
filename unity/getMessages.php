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

function getMessagesArray($groupID, $user, $connection)
{
    $messagesArray = 0;
    $messageGroup = getMessageGroupByID($groupID, $user, $connection);
    $lastMessage = "";
    $unreadMessageCount = 0;
    $lastMessageSource = "";
    $conversation = getConversation($user, $messageGroup, 'group', $connection);
    if ($conversation)
    {
        $lastMessage = $conversation[count($conversation) - 1];
        $lastMessageSource = $lastMessage['direction'] == RECEIVED_MESSAGE ? $lastMessage['sender'] : $user;
        foreach ($conversation as $message)
        {
            if ($message['direction'] == RECEIVED_MESSAGE)
            {
                if (!$message['isRead'])
                {
                    $unreadMessageCount++;
                }
            }
        }
    }
    if (is_int($groupID))
    {
        $messagesArray = array(
            "id" => $groupID,
            "title" => $messageGroup['title'],
            "author" => $messageGroup['author'],
            "lastMessage" => $lastMessage,
            "lastMessageSource" => $lastMessageSource,
            "unreadMessageCount" => $unreadMessageCount
        );
    }
    else
    {
        $messagesArray = array(
            "id" => $groupID,
            "lastMessage" => $lastMessage,
            "messageGroup" => $messageGroup,
            "lastMessageSource" => $lastMessageSource,
            "unreadMessageCount" => $unreadMessageCount
        );
    }
    return $messagesArray;
}

$user = validateUserForUnity($connection, $format);
if ($user)
{
    $users = getAllUsersExceptMe($user, $connection);
    $returnArray = array();
    $returnArray['privateMessages'] = array();

    foreach ($users as $u)
    {
        $messages = getAllMessagesFromRecipient($user, $u, "private", $connection);
        $unreadMessageCount = 0;
        if ($messages)
        {
            $lastMessage = $messages[count($messages) - 1];
            foreach ($messages as $message)
            {
                if (!$message['isRead'])
                {
                    $unreadMessageCount++;
                }
            }
        }
        else
        {
            $lastMessage = 0;
        }
        $userMessages = array(
            "user" => $u,
            "lastMessage" => $lastMessage,
            "unreadMessageCount" => $unreadMessageCount
        );
        array_push($returnArray['privateMessages'], $userMessages);
    }
    
    $returnArray['presetGroupMessages'] = array();
    
    // ALL preset Group
    
    $allMessages = getMessagesArray('all', $user, $connection);
    array_push($returnArray['presetGroupMessages'], $allMessages);
    
    // axoneron
    
    if (isMemberOfPresetGroup("axoneron", $user))
    {
        $axoneronMessages = getMessagesArray('axoneron', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $axoneronMessages);
    }
    
    // admin
    
    if (isMemberOfPresetGroup("admin", $user))
    {
        $adminMessages = getMessagesArray('admin', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $adminMessages);
    }
    
    // support
    
    if (isMemberOfPresetGroup("support", $user))
    {
        $supportMessages = getMessagesArray('support', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $supportMessages);
    }
    
    // medical
    
    if (isMemberOfPresetGroup("medical", $user))
    {
        $medicalMessages = getMessagesArray('medical', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $medicalMessages);
    }
    
    // commercial
    
    if (isMemberOfPresetGroup("commercial", $user))
    {
        $commercialMessages = getMessagesArray('commercial', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $commercialMessages);
    }
    
    // engage
    
    if (isMemberOfPresetGroup("engage", $user))
    {
        $engageMessages = getMessagesArray('engage', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $engageMessages);
    }
    
    // pixelmosaic
    
    if (isMemberOfPresetGroup("pixelmosaic", $user))
    {
        $pixelMosaicMessages = getMessagesArray('pixelmosaic', $user, $connection);
        array_push($returnArray['presetGroupMessages'], $pixelMosaicMessages);
    }
    
    // custom group
    
    $returnArray['customGroupMessages'] = array();
    $myGroups = getMyMessageGroups($user, $connection);

    if ($myGroups)
    {
        foreach ($myGroups as $group)
        {
        $customMessages = getMessagesArray($group['id'], $user, $connection);
        array_push($returnArray['customGroupMessages'], $customMessages);
        }
    }
    
    if ($returnArray)
    {
        echo packageDataForUnity($format, $returnArray, "messages");
    }
}