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
    $results = 0;
    $newMessageCount = 0;
    $recipientType = isset($_POST['recipient']) ? "private" : "group";
    $recipient = isset($_POST['recipient']) ? getUserById($_POST['recipient'], $connection) : getMessageGroupByID($_POST['group'], $user, $connection) ;
    $receivedMessages = getAllMessagesFromRecipient($user, $recipient, $recipientType, $connection);
    if ($receivedMessages)
    {
        foreach ($receivedMessages as $message)
        {
            if (!$message['isRead'])
            {
                $recordID = $message['recordID'];
                $data = $message["sender"]["id"];
                $data .= ",1,";
                $data .= $recipientType == "private" ? "0" : $message["messageGroup"]["id"];
                $query = "UPDATE user_" . $user['id'] . " SET data = '$data' WHERE id = '$recordID'";
                $result = $connection->query($query) ? 1 : $result;
                if ($result)
                {
                    $results = 1;
                    $newMessageCount++;
                }
            }
        }
        if ($results)
        {
            $recordData = array();
            $recordData['type'] = READ_CONVERSATION;
            $recordData['data'] = $recipient['id'] . "," . $recipientType . "," . $newMessageCount;
            $recordData['openEnd'] = "";
            $result = addRecordToUser($user, $recordData, $user, $connection);
        }
    }
}

