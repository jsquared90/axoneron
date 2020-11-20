<?php

/*************************
 *  BASE FUNCTIONS
 * ***********************
 */


/*
 * Base function for an admin pre-registering a new uer
 */
function preregisterUser($author, $connection)
{
    // ultimately add a step here to validate current user level for authorization
    // need to apply error handling for attempting to register a user with an exisiting email

    $code = 0;
    $errors = 0;
    $record = 0;
    
    $addUserResult = addUserToUsers($connection);
    if ($addUserResult['errors'] == 0)
    {
        $result = generateUserTable($author, $addUserResult['new_user'], $connection);
        if ($result)
        {
            aggregateAllGroupMessagesForNewUser($addUserResult['new_user'], $connection);
            $cc = $addUserResult['new_user']['cc'] ? "true" : "false";
            $recordData = array();
            $recordData['type'] = ADDED_USER;
            $recordData['data'] = $addUserResult['new_user']['id'];
            $recordData['cc'] = $cc;
            $recordData['openEnd'] = "";
            $result2 = addRecordToUser($author, $recordData, $author, $connection);
            if ($result2)
            {
                $record = getRecordByFootprint($recordData, $author, $connection);
                if ($record)
                {
                    $code = -1;
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 5);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

/*
 * Base function for a user completing an initial registration
 */
function registerUser($user, $connection)
{
    /*
     * Depending on decision by client, may need to fetch pre-reg record from admin user who initiated pre-reg of user
     * 1. Update record on table 'users' for user
     * 2. Post record on user's individual table
     */

    $code = 0;
    $errors = 0;
    $record = 0;
    
    $user = updateUser($user, $connection);
    if ($user)
    {
        $recordData = array();
        $recordData['type'] = SITE_REGISTRATION;
        $recordData['data'] = "";
        $recordData['openEnd'] = "";
        $result = addRecordToUser($user, $recordData, $user, $connection);
        if ($result)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 8);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 7);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 6);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

/*
 * Base function for a user modifying their information
 */
function modifyAccount($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $user = updateUser($user, $connection);
    
    if ($user)
    {
        $recordData = array();
        $recordData['type'] = MODIFIED_USER_ACCOUNT;
        $recordData['data'] = "";
        $recordData['openEnd'] = "";
        $result = addRecordToUser($user, $recordData, $user, $connection);
        if ($result)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 4);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record,
        "user" => $user
    );
}

function resetPassword($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    
    $id = $user['id'];
    $pw = md5($_POST['newPassword']);

    $query = "UPDATE users SET ";
    $query .= "password = '$pw'";
    $query .= " WHERE id = '$id'";
    $result = $connection->query($query);
    
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = RESET_PASSWORD;
        $recordData['data'] = "";
        $recordData['openEnd'] = "";
        $result2 = addRecordToUser($user, $recordData, $user, $connection);
        
        if ($result2)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 4);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

/*
 * Base function for an admin modifying another user's information
 */
function modifyUser($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $proxyUser = updateUser($user, $connection);
    
    if ($proxyUser)
    {
        $recordData = array();
        $recordData['type'] = MODIFIED_USER;
        $recordData['data'] = $proxyUser['id'];
        $recordData['openEnd'] = "";
        $result = addRecordToUser($user, $recordData, $user, $connection);
        if ($result)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function removeUser($userToRemove, $user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $result =  sendRecordToTrash(USER_RECORD, $userToRemove['id'], $connection);
    if ($result)
    {
        $recordData = array();
        $recordData['type'] = REMOVED_USER;
        $recordData['data'] = $userToRemove['id'];
        $recordData['openEnd'] = "";
        $result2 = addRecordToUser($user, $recordData, $user, $connection);
        if ($result2)
        {
            $record = getRecordByFootprint($recordData, $user, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function removeUsers($userIDs, $author, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    foreach ($userIDs as $userID)
    {
        $user = getUserById($userID, $connection);
        $result = removeUser($user, $author, $connection);
        if ($result['code'] > 0)
        {
            $code = 1;
            foreach ($result['errors'] as $error)
            {
                $errors = packageGeneralError($errors, $error);
            }
        }
    }
    if (!$errors)
    {
        $recordData = array();
        $recordData['type'] = REMOVED_USERS;
        $recordData['data'] = implode(",", $userIDs);
        $recordData['openEnd'] = "";
        $result2 = addRecordToUser($author, $recordData, $author, $connection);
        if ($result2)
        {
            $record = getRecordByFootprint($recordData, $author, $connection);
            if ($record)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 6);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 5);
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function removeHotelRequest($author, $congressID, $user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $returnRecord = 0;
    
    $record = getMostRecentHotelRequestForUser($author, $congressID, $connection);
    if ($record)
    {
        if ($record['type'] == HOTEL_REQUEST)
        {
            $prRecord = getPendingRequestByUserAndRecordID($record['id'], $author, $connection);
            if ($prRecord)
            {
                $result = removeFromPendingRequests($prRecord, $connection);
                if (!$result)
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 9);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 8);
            }
        }
        if ($code <= 0)
        {
            $records = $author['id'] . "," . $congressID;
            $result2 =  sendRecordToTrash(HOTEL_REQUEST_RECORD, $records, $connection);
            if ($result2)
            {
                $recordData = array();
                $recordData['type'] = CANCELLED_HOTEL_RESERVATION;
                $recordData['data'] = $congressID;
                $recordData['openEnd'] = "";
                $result3 = addRecordToUser($author, $recordData, $user, $connection);
                if ($result3)
                {
                    $returnRecord = getRecordByFootprint($recordData, $user, $connection);
                    if ($returnRecord)
                    {
                        $code = -1;
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 7);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 6);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 4);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

/*
 * Base function for a user submitting a hotel request
 */
function submitHotelRequest($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $recordData = array();
    $recordData['type'] = HOTEL_REQUEST;
    $recordData['data'] = packageHotelRequest();
    $openEnd = urlencode($_POST["openEnd"]);
    $openEnd = strlen($openEnd) > 1024 ? substr($openEnd, 0, 1021) . "..." : $openEnd;
    $recordData['openEnd'] = $openEnd;
    $result = addRecordToUser($user, $recordData, $user, $connection);
    if ($result)
    {
        $record = getRecordByFootprint($recordData, $user, $connection);
        if ($record)
        {
            $result2 = addToPendingRequests($record, $user, $connection);
            if ($result2)
            {
                $records = $user['id'] . "," . $_POST['congressID'];
                if (isTrashed(HOTEL_REQUEST_RECORD, $records, $connection))
                {
                    $result3 = removeFromTrash(HOTEL_REQUEST_RECORD, $records, $connection);
                    if ($result3)
                    {
                        $code = -1;
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 5);
                    }
                }
                else
                {
                    $code = -1;
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function modifyHotelRequest($user, $connection)
{
    // check to see if there is an existing pending request and delete it
    
    $mostRecentHotelRecord = getMostRecentReservationRequestForUser($user, $_POST['congressID'], $connection);
    if ($mostRecentHotelRecord['type'] == HOTEL_REQUEST || $mostRecentHotelRecord['type'] == MODIFIED_HOTEL_REQUEST)
    {
        $pR = getPendingRequestByUserAndRecordID($mostRecentHotelRecord['id'], $user, $connection);
        if ($pR)
        {
            removeFromPendingRequests($pR, $connection);
        }
    }
    $code = 0;
    $errors = 0;
    $record = 0;
    $recordData = array();
    $recordData['type'] = MODIFIED_HOTEL_REQUEST;
    $recordData['data'] = packageHotelRequest();
    $openEnd = urlencode($_POST["openEnd"]);
    $openEnd = strlen($openEnd) > 1024 ? substr($openEnd, 0, 1021) . "..." : $openEnd;
    $recordData['openEnd'] = $openEnd;
    $result = addRecordToUser($user, $recordData, $user, $connection);
    if ($result)
    {
        $record = getRecordByFootprint($recordData, $user, $connection);
        if ($record)
        {
            $result2 = addToPendingRequests($record, $user, $connection);
            if ($result2)
            {
                $code = -1;
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function setCongressAttendance($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $recordData = array();
    $recordData['type'] = ACK_CONGRESS_ATTENDANCE;
    $recordData['data'] = $_POST['congress'] . "," . $_POST['congressAckCC'];
    $recordData['openEnd'] = "";
    $result = addRecordToUser($user, $recordData, $user, $connection);
    if ($result)
    {
        $record = getRecordByFootprint($recordData, $user, $connection);
        if ($record)
        {
            $code = -1;
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function confirmHotelRequest($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $prID = $_POST["recordID"];
    $prRecord = getPendingRequestByID($prID, $user, $connection);
    $recordData = array();
    $recordData['type'] = HOTEL_CONFIRMATION;
    $recordData['data'] = packageHotelConfirmation();
    $openEnd = urlencode($_POST["openEnd"]);
    $openEnd = strlen($openEnd) > 1024 ? substr($openEnd, 0, 1021) . "..." : $openEnd;
    $recordData['openEnd'] = $openEnd;
    $sourceOfRequestResult = addRecordToUser($user, $recordData, $prRecord['sourceOfRequest'], $connection);
    if ($sourceOfRequestResult)
    {
        $record1 = getRecordByFootprint($recordData, $prRecord['sourceOfRequest'], $connection);
        if ($record1)
        {
            $recordData['type'] = HOTEL_REQUEST_COMPLETION;
            $recordData['data'] = $prRecord['sourceOfRequest']['id'] . "," . $prRecord['userRecord']['id'];
            $recordData['openEnd'] = '';
            $authorResult = addRecordToUser($user, $recordData, $user, $connection);
            if ($authorResult)
            {
                $removeResult = removeFromPendingRequests($prRecord, $connection);
                if ($removeResult)
                {
                    $record2 = getRecordByFootprint($recordData, $user, $connection);
                    if ($record2)
                    {
                        $record = $record1;
                        $code = -1;
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 7);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 6);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 5);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 4);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 3);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function confirmHospitalityRequest($user, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $prID = $_POST["recordID"];
    $prRecord = getPendingRequestByID($prID, $user, $connection);
    $bookingID = $prRecord['userRecord']['booking']['id'];
    $recordData = array();
    $recordData['type'] = HOSP_REQUEST_CONFIRMATION;
    $recordData['data'] = $_POST["congressID"] . "," . $bookingID;
    $openEnd = urlencode($_POST["openEnd"]);
    $openEnd = strlen($openEnd) > 1024 ? substr($openEnd, 0, 1021) . "..." : $openEnd;
    $recordData['openEnd'] = $openEnd;
    $sourceOfRequestResult = addRecordToUser($user, $recordData, $prRecord['sourceOfRequest'], $connection);
    if ($sourceOfRequestResult)
    {
        $record1 = getRecordByFootprint($recordData, $prRecord['sourceOfRequest'], $connection);
        if ($record1)
        {
            $recordData['type'] = HOSP_REQUEST_COMPLETION;
            $recordData['data'] = $prRecord['sourceOfRequest']['id'] . "," . $prRecord['userRecord']['id'];
            $recordData['openEnd'] = '';
            $authorResult = addRecordToUser($user, $recordData, $user, $connection);
            if ($authorResult)
            {
                $removeResult = removeFromPendingRequests($prRecord, $connection);
                if ($removeResult)
                {
                    $record2 = getRecordByFootprint($recordData, $user, $connection);
                    if ($record2)
                    {
                        $record = $record1;
                        $code = -1;
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 6);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 5);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 4);
            }
        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}


/*************************
 *  SUB FUNCTIONS
 * ***********************
 */

/*
 * For adding a new user to the table "users"
 */
function addUserToUsers($connection)
{
    $errors = 0;
    $new_user = 0;
    $email = $_POST['newUserEmail'];

    $duplicate = checkForDuplicateUser($email, $connection);

    if (!$duplicate)
    {
        $first = urlencode($_POST['newUserFirst']);
        $first = strlen($first) > 20 ? substr($first, 0, 20) : $first;
        $last = urlencode($_POST['newUserLast']);
        $last = strlen($last) > 30 ? substr($last, 0, 30) : $last;
        $level = $_POST['newUserLevel'];
        $phone = $_POST['newUserPhone'] == "" ? 0 : $_POST['newUserPhone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $title = urlencode($_POST['newUserTitle']);
        $title = strlen($title) > 32 ? substr($title, 0, 32) : $title;
        $role = $_POST['newUserRole'];

        $query = "INSERT INTO users (id, first, last, email, password, phone, title, level, role, imageURL) VALUES "
                . "(NULL, '$first', '$last', '$email', '', '$phone', '$title', '$level', '$role', '')";
        $result = $connection->query($query);
        if ($result)
        {
            $new_user = getUserByEmail($email, $connection);
            $new_user['cc'] = $_POST['newUserCC'];
        }
    }
    else
    {
        if (!$errors){ $errors = []; }
        $error = array(
            "code" => 2,
            "data" => $duplicate
        );
        array_push($errors, $error);
    }
    return getNewUserReturn($errors, $new_user);
}

function cleanUpUserLog($user, $connection)
{
    $records = array();
    $deletionArray = array();
    $query = "SELECT * FROM user_" . $user['id'] . " WHERE type = '" . RECEIVED_MESSAGE . "'";
    $result = $connection->query($query);
    $numRows = mysqli_num_rows($result);
    if ($numRows > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $record = packageUserRowIntoRecord($row, $connection);
            $valid = 0;
            foreach ($records as $r)
            {
                if ($r['data'] == $record['data'] &&
                $r['openEnd'] == $record['openEnd'] &&
                $r['entryDate'] == $record['entryDate'] &&
                $r['author'] == $record['author'])
                {
                    $valid = 1;
                    break;
                }
            }
            if ($valid)
            {
                array_push($deletionArray, $record['id']);
            }
            else
            {
                array_push($records, $record);
            }
        }
        foreach ($deletionArray as $duplicate)
        {
            $query = "DELETE FROM user_" . $user['id'] . " WHERE id = '" . $duplicate . "'";
            $result = $connection->query($query);
        }
    }
}

function aggregateAllGroupMessagesForNewUser($newUser, $connection)
{
    $messages = array();
    $users = getAllUsersExceptMe($newUser, $connection);
    foreach ($users as $user)
    {
        $messages = mergeGroupMessages($messages, $newUser, $user, "all", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "axoneron", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "admin", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "support", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "medical", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "commercial", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "engage", $connection);
        $messages = mergeGroupMessages($messages, $newUser, $user, "pixelmosaic", $connection);
    }
    $timestamps = array_column($messages, 'timeStamp');
    array_multisort($timestamps, SORT_ASC, $messages);
    
    foreach ($messages as $message)
    {
        $type = RECEIVED_MESSAGE;
        $data = $message['author'] . ",0," . $message['messageGroup']['id'];
        $openEnd = urlencode($message['message']);
        $authorID = $message['author'];
        $timestamp = date(getSqlDateFormat(), $message['timeStamp']);

        $query = "INSERT INTO user_" . $newUser['id'] . " (id, type, data, openEnd, entryDate, author) VALUES "
            . "(NULL, '$type', '$data', '$openEnd', '$timestamp', '$authorID')";
        $connection->query($query);
    }
}

function mergeGroupMessages($messages, $newUser, $user, $groupName, $connection)
{
    $valid = 1;
    if ($groupName != "all")
    {
        if (!isMemberOfPresetGroup($groupName, $newUser) || !isMemberOfPresetGroup($groupName, $user))
        {
            $valid = 0;
        }
    }
    if ($valid)
    {
        $messageGroup = getMessageGroupByID($groupName, $newUser, $connection);
        $newMessages = getAllMessagesToRecipient($user, $messageGroup, "group", $connection);
        if ($newMessages)
        {
            foreach($newMessages as $message)
            {
                $duplicate = checkForDuplicateMessage($message, $user, $newUser, $connection);
                if (!$duplicate)
                {
                    if (!$messages){ $messages = []; }
                    $messages = array_merge($messages, $newMessages);
                }
            }
        }
    }
    return $messages;
}

/*
 * This function is used to compare message data from a sender's log to the recipient's log
 * to determine if the message already exists in the recipient's log
 */
function checkForDuplicateMessage($message, $sender, $recipient, $connection)
{
    $valid = 0;
    $recipientID = $recipient['id'];
    $groupID = $message['messageGroup'] ? $message['messageGroup']['id'] : 0;
    $query = "SELECT * FROM user_" . $recipientID . " WHERE type = '" . RECEIVED_MESSAGE . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $message2 = packageMessageDataFromRow($row, $recipient, RECEIVED_MESSAGE, $connection);
            $groupID2 = $message2['messageGroup'] ? $message2['messageGroup']['id'] : 0;
            if ($sender['id'] == $message2['sender']['id'] &&
                $groupID == $groupID2 &&
                $message['message'] == $message2['message'] &&
                $message['timeStamp'] == $message2['timeStamp'])
            {
                $valid = 1;
            }
        }
    }
    return $valid;
}

function checkForDuplicateMessageGroupTitle($footprintData, $connection, $currentGroupID = 0)
{
    $result = 0;
            
    $groupTitle1 = strtolower($footprintData);
    $groupTitle2 = trim($groupTitle1);
    
    if ($groupTitle2 == "admin" ||
        $groupTitle2 == "all" ||
        $groupTitle2 == "axoneron" ||
        $groupTitle2 == "commercial" ||
        $groupTitle2 == "engage" ||
        $groupTitle2 == "medical" ||
        $groupTitle2 == "pixelmosaic" ||
        $groupTitle2 == "pixel mosaic" ||
        $groupTitle2 == "support")
    {
        if (!$result)
        {
            $result = array();
            $result['errors'] = array();
        }
        if ($groupTitle2 == "admin" ||
            $groupTitle2 == "all" ||
            $groupTitle2 == "axoneron" ||
            $groupTitle2 == "commercial" ||
            $groupTitle2 == "engage" ||
            $groupTitle2 == "medical" ||
            $groupTitle2 == "pixelmosaic" ||
            $groupTitle2 == "pixel mosaic" ||
            $groupTitle2 == "support")
        {
            $error = array(
                "code" => 20,
                "data" => $groupTitle2
            );
            array_push($result['errors'], $error);
        }
    }
    else
    {
        $messageGroups = getAllMessageGroups($connection);
        foreach ($messageGroups as $messageGroup)
        {
            if ($messageGroup['title'] == $groupTitle2)
            {
                if ($currentGroupID <= 0 || $currentGroupID != $messageGroup['id'])
                {
                    if (!$result)
                    {
                        $result = array();
                        $result['errors'] = array();
                    }
                    if ($messageGroup['title'] == $groupTitle2)
                    {
                        $error = array(
                            "code" => 21,
                            "data" => $groupTitle2
                        );
                        array_push($result['errors'], $error);
                    }
                }
            }
        }
    }
    return $result;
}

function checkForDuplicateUser($email, $connection)
{
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        return "There is a user registered with this email already, " . urldecode($email) . ".";
    }
}

/*
 * Creates a new table 'user_X', where X is the user's record id in the table 'users'
 * This table is used to record all actions from that user and other user actions directed for that user
 */
function generateUserTable($author, $new_user, $connection)
{
    $query = "CREATE TABLE user_" . $new_user['id'] . " (";
    $query .= "id int(7) NOT NULL AUTO_INCREMENT,";
    $query .= "type varchar(32) NOT NULL,";
    $query .= "data varchar(1024) NOT NULL,";
    $query .= "openEnd varchar(1024) NOT NULL,";
    $query .= "entryDate datetime NOT NULL,";
    $query .= "author int(5) NOT NULL,";
    $query .= "PRIMARY KEY (id))";
    $result = $connection->query($query);

    return $result;
}

/*
 * Used any time a record for a user in 'users' needs to be updated
 */
function updateUser($user, $connection)
{
    //$user = 0;
    
    $previousLevel = 0;
    $previousRole = 0;

    if (isset($_POST[POST_REGISTER_USER]))
    {
        $first = urlencode($_POST['first']);
        $first = strlen($first) > 20 ? substr($first, 0, 20) : $first;
        $last = urlencode($_POST['last']);
        $last = strlen($last) > 30 ? substr($last, 0, 30) : $last;
        $pw1 = md5($_POST['pw1']);
        $pw2 = md5($_POST['pw2']);
        $phone = $_POST['phone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $title = urlencode($_POST['title']);
        $title = strlen($title) > 32 ? substr($title, 0, 32) : $title;
        $role = $_POST['role'];
        $id = $_POST['id'];
        $imageURL = '';
    }
    elseif (isset($_POST[POST_MODIFY_ACCOUNT]))
    {
        $first = urlencode($_POST['newUserFirst']);
        $first = strlen($first) > 20 ? substr($first, 0, 20) : $first;
        $last = urlencode($_POST['newUserLast']);
        $last = strlen($last) > 30 ? substr($last, 0, 30) : $last;
        $pw1 = $_POST['newPassword'] != "" ? md5($_POST['newPassword']) : $user['password'];
        $phone = $_POST['newUserPhone'] == "" ? 0 : $_POST['newUserPhone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $title = urlencode($_POST['newUserTitle']);
        $title = strlen($title) > 32 ? substr($title, 0, 32) : $title;
        if ($user['role'] != $_POST['newUserRole'])
        {
            $previousRole = $user['role'];
        }
        $role = $_POST['newUserRole'];
        $id = $user['id'];
        
        $imageURL = $user['imageURL'];
        $imageUploadReturn = uploadUserImage($user);
        $imageURL = $imageUploadReturn['code'] < 0 ? rawurlencode($imageUploadReturn['saveName']) : $imageURL;
    }
    else if (isset($_POST[POST_MODIFY_USER]))
    {
        $proxyUser = getUserById($_POST['userID'], $connection);
        $first = urlencode($_POST['newUserFirst']);
        $first = strlen($first) > 20 ? substr($first, 0, 20) : $first;
        $last = urlencode($_POST['newUserLast']);
        $last = strlen($last) > 30 ? substr($last, 0, 30) : $last;
        $pw1 = $proxyUser['password'];
        if ($proxyUser['level'] != $_POST['newUserLevel'])
        {
            $previousLevel = $proxyUser['level'];
        }
        $level = $_POST['newUserLevel'];
        $phone = $_POST['newUserPhone'] == "" ? 0 : $_POST['newUserPhone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $title = urlencode($_POST['newUserTitle']);
        $title = strlen($title) > 32 ? substr($title, 0, 32) : $title;
        if ($proxyUser['role'] != $_POST['newUserRole'])
        {
            $previousRole = $proxyUser['role'];
        }
        $role = $_POST['newUserRole'];
        $id = $proxyUser['id'];
    }

    $query = "UPDATE users SET ";
    $query .= "first = '$first', ";
    $query .= "last = '$last', ";
    $query .= "password = '$pw1', ";
    $query .= "phone = '$phone', ";
    $query .= "title = '$title', ";
    if (isset($_POST[POST_MODIFY_USER]))
    {
        $query .= "level = '$level', ";
    }
    $query .= "role = '$role'";
    if (isset($_POST[POST_MODIFY_ACCOUNT]))
    {
        $query .= ", imageURL = '$imageURL'";
    }
    else
    {
        
    }
    $query .= " WHERE id = '$id'";
    $result = $connection->query($query);
    if ($result)
    {
        $user = getUserById($id, $connection);
        if ($previousRole || $previousLevel)
        {
            aggregateAllGroupMessagesForNewUser($user, $connection);
        }
    }
    else
    {
        $user = 0;
    }

    return $user;

}

function uploadUserImage($user)
{
    $code = 0;
    $saveLocation = 0;
    $saveName = 0;
    if (isset($_FILES))
    {
        if (isset($_FILES['imageFile']))
        {
            if (isset($_FILES['imageFile']['name']))
            {
                if ($_FILES['imageFile']['name'] != '')
                {
                    if ($_FILES["imageFile"]["size"] > FILE_IMPORT_SIZE_LIMIT)
                    {
                        $code = 5;
                    }
                    else
                    {
                        $raw_file = basename($_FILES["imageFile"]["name"]);
                        $extension = strtolower(pathinfo($raw_file,PATHINFO_EXTENSION));
                        if($extension != "png" && $extension != "jpg" && $extension != "jpeg")
                        {
                            $code = 6;
                        }
                        else
                        {
                            $target_dir = USER_IMAGES_PATH;
                            $fileDateString = date(getSaveForFileDateFormat(), time());
                            $saveName = $user['first'] . "_" . $user['last'] . "_" . $fileDateString . "." . $extension;
                            $saveLocation = $target_dir . $saveName;
                            if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $saveLocation))
                            {
                                $code = -1;
                                createThumbnail($saveLocation, USER_IMAGES_THUMBS_PATH . $saveName, 256);
                            }
                            else
                            {
                                $code = 7;
                            }
                        }
                    }
                }
                else
                {
                    $code = 4;
                }
            }
            else
            {
                $code = 3;
            }
        }
        else
        {
            $code = 2;
        }
    }
    else
    {
        $code = 1;
    }
    
    return array(
        "code" => $code,
        "saveName" => $saveName,
        "saveLocation" => $saveLocation
            );
}

function getUserByEmail($email, $connection)
{
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($query);
    $user = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
        }
    }
    return $user;
}

function getAllAuthors($connection)
{
    $query = "SELECT * FROM messageGroups";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $messageGroups = packageMessageGroupFromRow($row, $connection);
        }
    }
    return $messageGroups;
}

function getUserById($id, $connection)
{
    $query = "SELECT * FROM users WHERE id = '$id'";
    $result = $connection->query($query);
    $user = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
        }
    }
    return $user;
}

function getUserByFullValidation($id, $password, $connection)
{
    $password = md5($password);
    $query = "SELECT * FROM users WHERE id = '$id' AND password = '$password'";
    $result = $connection->query($query);
    $user = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
        }
    }
    return $user;
}

function getUserByFirstLast($first, $last, $connection)
{
    $query = "SELECT * FROM users WHERE first = '$first' AND last = '$last'";
    $result = $connection->query($query);
    $user = 0;
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
        }
    }
    return $user;
}

function getAllUsers($connection)
{
    $users = 0;
    $query = "SELECT * FROM users";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
            if ($user)
            {
                if (!$users){ $users = []; }
                array_push($users, $user);
            }
        }
        usort($users, function($a, $b)
        {
            return $a['first'] <=> $b['first'];
        });
    }
    return $users;
}

function getAllUsersExceptMe($me, $connection)
{
    $users = 0;
    $query = "SELECT * FROM users";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            if ($me['id'] != $row['id'])
            {
                $user = packageUserRowIntoArray($row, $connection);
                if ($user)
                {
                    if (!$users){ $users = []; }
                    array_push($users, $user);
                }
            }
        }
        usort($users, function($a, $b)
        {
            return $a['first'] <=> $b['first'];
        });
    }
    return $users;
}

function getAllSupportUsers($connection)
{
    $users = 0;
    $query = "SELECT * FROM users WHERE level = '3'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $user = packageUserRowIntoArray($row, $connection);
            if ($user)
            {
                if (!$users){ $users = []; }
                array_push($users, $user);
            }
        }
        usort($users, function($a, $b)
        {
            return $a['first'] <=> $b['first'];
        });
    }
    return $users;
}

function getCongressAttendance($user, $congressID, $connection)
{
    $attending = -1;
    $userID = $user["id"];
    $query = "SELECT * FROM user_" . $userID . " WHERE type = '" . ACK_CONGRESS_ATTENDANCE . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $data = explode(",", $row['data']);
            if ($data[0] == $congressID)
            {
                // this should set to the latest setting
                $attending = $data[1];
            }
        }
    }
    if ($attending == -1)
    {
        $hotelRecord = getMostRecentReservationRequestForUser($user, $congressID, $connection);
        if ($hotelRecord)
        {
            $attending = 1;
        }
    }
    return $attending;
}

function getUnviewedCongressCount($user, $connection)
{
    $unviewedCongresses = 0;
    $congresses = getAllCongresses($connection);
    foreach ($congresses as $congress)
    {
        if (!congressIsInThePast($congress))
        {
            if (getCongressAttendance($user, $congress['id'], $connection) == -1)
            {
                $unviewedCongresses++;
            }
        }
    }
    return $unviewedCongresses;
}

function getAllUsersAttendingCongress($congress, $connection)
{
    $users = 0;
    $allUsers = getAllUsers($connection);
    foreach ($allUsers as $user)
    {
        $attending = getCongressAttendence($user, $congress['id'], $connection);
        if ($attending == 1)
        {
            if (!$users){ $users = []; }
            array_push($users, $user);
        }
    }
    return $users;
}

function getUnviewedReservationCount($user, $connection)
{
    $unviewedReservations = 0;
    $hotelRezRecords = getAllMostRecentHotelReservationsForUser($user, $connection);
    if ($hotelRezRecords)
    {
        foreach ($hotelRezRecords as $hotelRezRecord)
        {
            if ($hotelRezRecord['type'] == HOTEL_CONFIRMATION)
            {
                if (!reservationHasBeenViewed($hotelRezRecord, $user, $connection))
                {
                    $unviewedReservations++;
                }
            }
        }
    }
    $hospBookings = getAllHospBookingsForUser($user, $connection);
    if ($hospBookings)
    {
        foreach ($hospBookings as $booking)
        {
            if ($booking['openEnd'] != "" && isset($booking['adminComment']))
            {
                $record = getRecordByID($booking['confirmationRecordID'], $user, $connection);
                if (!reservationHasBeenViewed($record, $user, $connection))
                {
                    $unviewedReservations++;
                }
            }
        }
    }
    
    return $unviewedReservations;
}

function reservationHasBeenViewed($record, $user, $connection)
{
    $valid = 0;
    $userID = $user["id"];
    $query = "SELECT * FROM user_" . $userID . " WHERE type = '" . CONFIRMATION_VIEWED . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            if ($row['data'] == $record['id'])
            {
                $valid = 1;
            }
        }
    }
    return $valid;
}

function setReservationToViewed($record, $user, $connection)
{
    if (!reservationHasBeenViewed($record, $user, $connection))
    {
        $recordData['type'] = CONFIRMATION_VIEWED;
        $recordData['data'] = $record['id'];
        $recordData['openEnd'] = '';
        addRecordToUser($user, $recordData, $user, $connection);
    }
}

function packageUserRowIntoArray($row, $connection)
{
    $user = 0;
    
    if (!isTrashed(USER_RECORD, $row['id'], $connection))
    {
        $user = array(
            'id' => $row['id'],
            'first' => urldecode($row['first']),
            'last' => urldecode($row['last']),
            'email' => $row['email'],
            'phone' => $row['phone'],
            'title' => urldecode($row['title']),
            'password' => $row['password'],
            'level' => $row['level'],
            'role' => $row['role'],
            'imageURL' => $row['imageURL']
        );
    }
    
    return $user;
}

function packageUserRowIntoRecord($row, $connection)
{
    if (!isTrashed(USER_RECORD, $row['id'], $connection))
    {
        $record = array(
            'id' => $row['id'],
            'data' => ($row['data']),
            'openEnd' => ($row['openEnd']),
            'entryDate' => $row['entryDate'],
            'author' => $row['author']
        );
    }
    return $record;
}

function getUserRole($user)
{
    switch ($user['role'])
    {
        case 'commercial':
            return "Commercial";
        case 'medical':
            return "Medical";
        case 'admin':
            return "Administrator";
        case 'support':
            return "Support";
        default:
            return "Undefined";
    }
}


/*************************
 *  PENDING REQUESTS
 * ***********************
 */

function getAllPendingRequests($user, $connection)
{
    $pendingRequests = 0;
    if ($user['level'] > 1)
    {
        $query = "SELECT * FROM pending_requests";
        $result = $connection->query($query);
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $sourceOfRequest = getUserById($row['userID'], $connection);
                if ($sourceOfRequest)
                {
                    $userRecord = getRecordByID($row["recordID"], $sourceOfRequest, $connection);
                    if ($userRecord['type'] == HOTEL_REQUEST || $userRecord['type'] == MODIFIED_HOTEL_REQUEST)
                    {
                        $congressID = $userRecord['reservation']['congressID'];
                    }
                    else
                    {
                        $congressID = $userRecord['booking']['room']['congressID'];
                    }
                    $congress = getCongressById($congressID, $connection);
                    if ($congress)
                    {
                        $valid = 1;
                        $valid = congressIsInThePast($congress) ? 0 : $valid;
                        $records = $sourceOfRequest['id'] . "," . $congressID;
                        if (($userRecord['type'] == HOTEL_REQUEST || $userRecord['type'] == MODIFIED_HOTEL_REQUEST) && isTrashed(HOTEL_REQUEST_RECORD, $records, $connection))
                        {
                            $valid = 0;
                        }
                        if ($valid)
                        {
                            $pendingRequest = packagePendingRequestRecord($row, $sourceOfRequest, $userRecord);
                            if (!$pendingRequests){ $pendingRequests = []; }
                            array_push($pendingRequests, $pendingRequest);
                        }
                    }
                }
            }
        }
    }

    return $pendingRequests;
}

function getPendingRequestByID($prID, $user, $connection)
{
    $pendingRequest = 0;
    if ($user['level'] > 1)
    {
        $query = "SELECT * FROM pending_requests WHERE id = " . $prID;
        $result = $connection->query($query);
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $sourceOfRequest = getUserById($row['userID'], $connection);
                if ($sourceOfRequest)
                {
                    $userRecord = getRecordByID($row["recordID"], $sourceOfRequest, $connection);
                    $pendingRequest = packagePendingRequestRecord($row, $sourceOfRequest, $userRecord);
                }
            }
        }
    }
    return $pendingRequest;
}

function getPendingRequestByUserAndRecordID($userRecordID, $user, $connection)
{
    $userID = $user['id'];
    $pendingRequest = 0;
    $query = "SELECT * FROM pending_requests WHERE userID = " . $userID . " AND recordID = " . $userRecordID;
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $sourceOfRequest = getUserById($row['userID'], $connection);
            if ($sourceOfRequest)
            {
                $userRecord = getRecordByID($row["recordID"], $sourceOfRequest, $connection);
                $pendingRequest = packagePendingRequestRecord($row, $sourceOfRequest, $userRecord);
            }
        }
    }
    return $pendingRequest;
}

function getPendingRequestsForCongress($congress, $connection)
{
    $pendingRequests = 0;
    $query = "SELECT * FROM pending_requests";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $sourceOfRequest = getUserById($row['userID'], $connection);
            if ($sourceOfRequest)
            {
                $userRecord = getRecordByID($row["recordID"], $sourceOfRequest, $connection);
                $congress2 = parseCongressFromRecord($userRecord, $connection);
                if ($congress['id'] == $congress2['id'])
                {
                    if (!$pendingRequests){ $pendingRequests = []; }
                    $pendingRequest = packagePendingRequestRecord($row, $sourceOfRequest, $userRecord);
                    array_push($pendingRequests, $pendingRequest);
                }
            }
        }
    }

    return $pendingRequests;
}

function addToPendingRequests($record, $user, $connection)
{
    $recordID = $record["id"];
    $userID = $user["id"];
    $query = "INSERT INTO pending_requests (id, userID, recordID) VALUES (NULL, '$userID', '$recordID')";
    $result = $connection->query($query);
    return $result;
}

function removeFromPendingRequests($prRecord, $connection)
{
    $id = $prRecord["recordID"];
    $query = "DELETE FROM pending_requests WHERE id = '$id'";
    $result = $connection->query($query);
    return $result;
}

function packagePendingRequestRecord($row, $sourceOfRequest, $userRecord)
{
    $request = 0;
    if ($sourceOfRequest)
    {
        $request = array(
            'recordID' => $row['id'],
            'sourceOfRequest' => $sourceOfRequest,
            'userRecord' => $userRecord
        );
    }
    return $request;
}

/*************************
 *  GENERAL USER RECORDS
 * ***********************
 */

/*
 * General function for adding a record of user action to a user's indidivual table
 *
 * Types are pre-defined
 * Record Data is a comma delimited string of data that is uniquely affiliated with the type (key,values)
 * Open End is a field that supports 1024 characters for an entry that includes open ended input (eg. comments)
 * Entry Date is the timestamp of the entry (executed in SQL)
 * Author is the id of the user who is responsible for the action
 */

function addRecordToUser($author, $recordData, $user, $connection)
{
    $type = $recordData['type'];
    $data = $recordData['data'];
    $openEnd = $recordData['openEnd'];
    $authorID = $author['id'];

    $query = "INSERT INTO user_" . $user['id'] . " (id, type, data, openEnd, entryDate, author) VALUES "
            . "(NULL, '$type', '$data', '$openEnd', CURRENT_TIMESTAMP, '$authorID')";
    $result = $connection->query($query);

    return $result;
}

/*
 * currently only the following returns an extended set of data:
 * - Hotel Requests
 * - Hotel Confirmations
 * - Hospitality Room Bookings
 *
 * Anything else returns a default set of data
 */
function getRecordByID($recordID, $user, $connection)
{
    $record = 0;
    $userID = $user["id"];
    $query = "SELECT * FROM user_" . $userID . " WHERE id = '$recordID'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $record = array(
                "id" => $row["id"],
                "sourceID" => $userID,
                "type" => $row["type"],
                "openEnd" => urldecode($row["openEnd"]),
                "timeStamp" => strtotime($row["entryDate"]),
                "authorID" => $row["author"],
                "data" => $row['data']
            );
            switch ($record['type'])
            {
                case HOTEL_REQUEST:
                    $record['reservation'] = parseHotelRequestData($row["data"]);
                    break;
                case MODIFIED_HOTEL_REQUEST:
                    $record['reservation'] = parseHotelRequestData($row["data"]);
                    break;
                case HOTEL_CONFIRMATION:
                    $record['reservation'] = parseHotelReservationData($row["data"]);
                    break;
                case HOSP_ROOM_BOOKED:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                case MODIFIED_HOSP_ROOM_BOOKING:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                case HOSP_REQUEST_CONFIRMATION:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                default:
                    break;
            }
        }
    }
    return $record;
}

/*
 * currently only the following returns an extended set of data:
 * - Hotel Requests
 * - Hotel Confirmations
 * - Hospitality Room Bookings
 *
 * Anything else returns a default set of data
 */
function getRecordByFootprint($recordData, $user, $connection)
{
    $record = 0;
    $userID = $user["id"];
    $type = $recordData['type'];
    $data = $recordData['data'];
    $openEnd = $recordData['openEnd'];
    $query = "SELECT * FROM user_" . $userID . " WHERE type = '$type' AND data = '$data' AND openEnd = '$openEnd'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $record = array(
                "id" => $row["id"],
                "sourceID" => $userID,
                "type" => $row["type"],
                "openEnd" => urldecode($row["openEnd"]),
                "timeStamp" => strtotime($row["entryDate"]),
                "authorID" => $row["author"]
            );
            switch ($record['type'])
            {
                case HOTEL_REQUEST:
                    $record['reservation'] = parseHotelRequestData($row["data"]);
                    break;
                case MODIFIED_HOTEL_REQUEST:
                    $record['reservation'] = parseHotelRequestData($row["data"]);
                    break;
                case HOTEL_CONFIRMATION:
                    $record['reservation'] = parseHotelReservationData($row["data"]);
                    break;
                case HOSP_ROOM_BOOKED:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                case MODIFIED_HOSP_ROOM_BOOKING:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                case HOSP_REQUEST_CONFIRMATION:
                    $record['booking'] = parseHospRoomBookingData($row["data"], $connection);
                    break;
                default:
                    $record['data'] = $row['data'];
                    break;
            }
        }
    }
    return $record;
}

function parseCongressFromRecord($record, $connection)
{
    $congress = 0;
    if ($record['type'] == HOTEL_REQUEST || $record['type'] == MODIFIED_HOTEL_REQUEST || $record['type'] == HOTEL_CONFIRMATION)
    {
        $congress = getCongressById($record['reservation']['congressID'], $connection);
    }
    else if ($record['type'] == HOSP_ROOM_BOOKED || $record['type'] == MODIFIED_HOSP_ROOM_BOOKING || $record['type'] == HOSP_REQUEST_CONFIRMATION)
    {
        $congress = getCongressById($record['booking']['room']['congressID'], $connection);
    }
    return $congress;
}

/*************************
 *  HOTEL RESERVATION RECORDS
 * ***********************
 */

function getAllMostRecentHotelReservationsForUser($user, $connection)
{
    $records = 0;
    $userID = $user["id"];
    $congresses = getAllCongresses($connection);
    if ($congresses)
    {
        foreach ($congresses as $congress)
        {
            if (!congressIsInThePast($congress))
            {
                $congressID = $congress["id"];
                $record = getMostRecentReservationRequestForUser($user, $congressID, $connection);
                if ($record)
                {
                    if (!$records){ $records = []; }
                    array_push($records, $record);
                }
            }
        }
    }
    return $records;
}

function getHotelReservationDataForUser($user, $congressID, $connection)
{
    $records = $user['id'] . "," . $congressID;
    if (!isTrashed(HOTEL_REQUEST_RECORD, $records, $connection))
    {
        $records = array();
        $userID = $user["id"];
        $query = "SELECT * FROM user_" . $userID . " WHERE type = '" . HOTEL_REQUEST . "' OR type = '" . MODIFIED_HOTEL_REQUEST . "' OR type = '" . HOTEL_CONFIRMATION . "'";
        $result = $connection->query($query);
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                if ($row['type'] == HOTEL_REQUEST || $row['type'] == MODIFIED_HOTEL_REQUEST)
                {
                    $reservation = parseHotelRequestData($row["data"]);
                }
                else
                {
                    $reservation = parseHotelReservationData($row["data"]);
                }
                if ($reservation["congressID"] == $congressID)
                {
                    $record = array(
                        "id" => $row["id"],
                        "type" => $row["type"],
                        "reservation" => $reservation,
                        "openEnd" => urldecode($row["openEnd"]),
                        "timeStamp" => strtotime($row["entryDate"]),
                        "authorID" => $row["author"]
                    );
                    array_push($records, $record);
                }
            }
            if (count($records) > 0)
            {
                return $records;
            }
            else
            {
                return 0;
            }
        }
        else
        {
            return 0;
        }
    }
    else
    {
        return 0;
    }
}

function getMostRecentHotelRequestForUser($user, $congressID, $connection)
{
    $records = getHotelReservationDataForUser($user, $congressID, $connection);
    $targetRecord = 0;
    if ($records)
    {
        $mostRecentTimeStamp = 0;
        foreach ($records as $record)
        {
            if ($record['type'] == HOTEL_REQUEST || $record['type'] == MODIFIED_HOTEL_REQUEST)
            {
                if ($record["timeStamp"] > $mostRecentTimeStamp)
                {
                    $mostRecentTimeStamp = $record["timeStamp"];
                    $targetRecord = $record;
                }
            }
        }
    }
    return $targetRecord;
}

function getPreviousHotelRequestForUser($user, $congressID, $connection)
{
    $currentRecord = getMostRecentHotelRequestForUser($user, $congressID, $connection);
    $records = getHotelReservationDataForUser($user, $congressID, $connection);
    $targetRecord = 0;
    if ($records)
    {
        $mostRecentTimeStamp = 0;
        foreach ($records as $record)
        {
            if ($record['id'] != $currentRecord['id'])
            {
                if ($record['type'] == HOTEL_REQUEST || $record['type'] == MODIFIED_HOTEL_REQUEST)
                {
                    if ($record["timeStamp"] > $mostRecentTimeStamp)
                    {
                        $mostRecentTimeStamp = $record["timeStamp"];
                        $targetRecord = $record;
                    }
                }
            }
        }
    }
    return $targetRecord;
}

function getMostRecentReservationRequestForUser($user, $congressID, $connection)
{
    $records = getHotelReservationDataForUser($user, $congressID, $connection);
    $targetRecord = 0;
    if ($records)
    {
        $mostRecentTimeStamp = 0;
        foreach ($records as $record)
        {
            if ($record["timeStamp"] > $mostRecentTimeStamp)
            {
                $mostRecentTimeStamp = $record["timeStamp"];
                $targetRecord = $record;
            }
        }
    }
    if ($targetRecord['type'] == HOTEL_CONFIRMATION)
    {
        $originalRecord = getMostRecentHotelRequestForUser($user, $congressID, $connection);
        if ($originalRecord)
        {
            $targetRecord['reservation']['specialRequest'] = $originalRecord['openEnd'];
        }
    }
    return $targetRecord;
}

function getPreviousReservationRequestForUser($user, $congressID, $connection)
{
    $currentRecord = getMostRecentReservationRequestForUser($user, $congressID, $connection);
    $records = getHotelReservationDataForUser($user, $congressID, $connection);
    $targetRecord = 0;
    if ($records)
    {
        $mostRecentTimeStamp = 0;
        foreach ($records as $record)
        {
            if ($record['id'] != $currentRecord['id'])
            {
                if ($record["timeStamp"] > $mostRecentTimeStamp)
                {
                    $mostRecentTimeStamp = $record["timeStamp"];
                    $targetRecord = $record;
                }
            }
        }
    }
    if ($targetRecord['type'] == HOTEL_CONFIRMATION)
    {
        $originalRecord = getPreviousHotelRequestForUser($user, $congressID, $connection);
        if ($originalRecord)
        {
            $targetRecord['reservation']['specialRequest'] = $originalRecord['openEnd'];
        }
    }
    return $targetRecord;
}

function deleteUserRecord($user, $record, $connection)
{
    $id = $record['id'];
    $query = "DELETE FROM user_" . $user['id'] . " WHERE id = '$id'";
    $result = $connection->query($query);
    return $result;
}

/*************************
 *  HOSPITALITY BOOKING RECORDS
 * ***********************
 */

function injectHospRoomBookingConfirmation($booking, $connection)
{
    $userID = $booking["author"]["id"];
    $congressID = $booking["room"]["congressID"];

    $query = "SELECT * FROM user_" . $userID . " WHERE type = '" . HOSP_REQUEST_CONFIRMATION . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $data = explode(",",$row["data"]);
            if ($congressID == $data[0] && $booking["id"] == $data[1])
            {
                $booking['confirmationRecordID'] = $row['id'];
                $booking["adminComment"] = urldecode($row["openEnd"]);
            }
        }
    }

    return $booking;
}

function getUserRecordForHospBooking($booking, $connection)
{
    $userRecord = 0;
    $recordID = 0;
    
    $userID = $booking["author"]["id"];
    $congressID = $booking["room"]["congressID"];
    $query = "SELECT * FROM user_" . $userID . " WHERE type = '" . HOSP_ROOM_BOOKED . "' OR type = '" . MODIFIED_HOSP_ROOM_BOOKING . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $data = explode(",", $row['data']);
            if ($data[0] == $congressID && $data[1] == $booking['id'])
            {
                $recordID = $row['id'];
            }
        }
    }
    if ($recordID)
    {
        $user = getUserById($userID, $connection);
        $userRecord = getRecordByID($recordID, $user, $connection);
    }
    return $userRecord;
}

/*************************
 *  MESSAGE RECORDS
 * ***********************
 */

function getAllMessagesFromRecipient($me, $recipient, $recipientType, $connection)
{
    $messages = 0;
    $myID = $me['id'];
    $query = "SELECT * FROM user_" . $myID . " WHERE type = '" . RECEIVED_MESSAGE . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $valid = 0;
            $message = packageMessageDataFromRow($row, $me, RECEIVED_MESSAGE, $connection);
            if (!$message['messageGroup'] && $recipientType == 'private')
            {
                $valid = $message['sender']['id'] == $recipient['id'] ? 1 : $valid;
            }
            else if ($message['messageGroup'] && $recipientType == 'group') 
            {
                if (is_array($recipient))
                {
                    $valid = $message['messageGroup']['id'] == $recipient['id'] ? 1 : $valid;
                }
            }
            if ($valid)
            {
                if (!$messages){ $messages = []; }
                array_push($messages, $message);
            }
        }
    }
    return $messages;
}

function getAllMessagesToRecipient($me, $recipient, $recipientType, $connection)
{
    $messages = 0;
    $myID = $me['id'];
    $query = "SELECT * FROM user_" . $myID . " WHERE type = '" . SENT_MESSAGE . "'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $valid = 0;
            $message = packageMessageDataFromRow($row, $me, SENT_MESSAGE, $connection);
            if (!$message['messageGroup'] && $recipientType == 'private')
            {
                $valid = $message['recipient']['id'] === $recipient['id'] ? 1 : $valid;
            }
            else if ($message['messageGroup'] && $recipientType == 'group') 
            {
                if ($message['messageGroup']['id'] == $recipient['id'])
                {
                    $valid = 1;
                    //echo $message['messageGroup']['id'] . " : " . $recipient['id'] . "<br/>";
                }
            }
            if ($valid)
            {
                if (!$messages){ $messages = []; }
                array_push($messages, $message);
            }
        }
    }
    return $messages;
}

function isPresetGroup($id)
{
    if ($id == "all" ||
        $id == "axoneron" ||
        $id == "engage" ||
        $id == "pixelmosaic" ||
        $id == "pixel mosaic" ||
        $id == "admin" ||
        $id == "support" ||
        $id == "medical" ||
        $id == "commercial")
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

function isMemberOfPresetGroup($id, $user)
{
    $valid = 0;
    switch ($id)
    {
        case "all":
            $valid = 1;
            break;
        case "support":
            $valid = $user['role'] == 'support' ? 1 : 0;
            break;
        case "admin":
            $valid = $user['role'] == 'admin' ? 1 : 0;
            break;
        case "medical":
            $valid = $user['role'] == 'medical' ? 1 : 0;
            break;
        case "commercial":
            $valid = $user['role'] == 'commercial' ? 1 : 0;
            break;
        case "pixelmosaic":
            if (strpos(strtolower($user['title']), 'pixel mosaic') !== false)
            {
                $valid = 1;
            }
            break;
        case "engage":
            if (strpos(strtolower($user['title']), 'engage labs') !== false)
            {
                $valid = 1;
            }
            break;
        case "axoneron":
            if (strpos(strtolower($user['title']), 'axoneron') !== false ||
                    $user['level'] < 3)
            {
                $valid = 1;
            }
            break;
        default:
            $valid = strtolower($user['title']) == $id ? 1 : $valid;
            break;
    }
    return $valid;
}

function getPresetDisplayName($id)
{
    switch($id)
    {
        case "all":
            return "Everyone";
        case "axoneron":
            return "Team Axoneron";
        case "engage":
            return "Engage Labs";
        case "pixelmosaic":
            return "Pixel Mosaic";
        case "admin":
            return "Administrators";
        case "support":
            return "Support";
        case "medical":
            return "Medical";
        case "commercial":
            return "Commercial";
        default:
            return "Unkown";
    }
}

function getPresetClassName($id)
{
    switch($id)
    {
        case "all":
            return "allGroupImageDIV";
        case "axoneron":
            return "axoneronGroupImageDIV";
        case "engage":
            return "engageGroupImageDIV";
        case "pixelmosaic":
            return "pmGroupImageDIV";
        case "admin":
            return "adminGroupImageDIV";
        case "support":
            return "supportGroupImageDIV";
        case "medical":
            return "medicalGroupImageDIV";
        case "commercial":
            return "commercialGroupImageDIV";
        default:
            return "groupImageDIV";
    }
}

function getConversation($me, $recipient, $recipientType, $connection)
{
    $allMessages = 0;
    $sentMessages = getAllMessagesToRecipient($me, $recipient, $recipientType, $connection);
    $receivedMessages = getAllMessagesFromRecipient($me, $recipient, $recipientType, $connection);
    if ($receivedMessages && $sentMessages)
    {
        $allMessages = array_merge($sentMessages, $receivedMessages);
    }
    else if ($sentMessages)
    {
        $allMessages = $sentMessages;
    }
    else if ($receivedMessages)
    {
        $allMessages = $receivedMessages;
    }
    if ($allMessages)
    {
        $timestamps = array_column($allMessages, 'timeStamp');
        array_multisort($timestamps, SORT_ASC, $allMessages);
    }
    //array_multisort($timestamps, SORT_DESC, $allMessages);
    return $allMessages;
}

function setConversionToRead($me, $connection)
{
    $results = 0;
    $newMessageCount = 0;
    $recipientType = isset($_GET['recipient']) ? "private" : "group";
    $recipient = isset($_GET['recipient']) ? getUserById($_GET['recipient'], $connection) : getMessageGroupByID($_GET['group'], $me, $connection) ;
    $receivedMessages = getAllMessagesFromRecipient($me, $recipient, $recipientType, $connection);
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
                $query = "UPDATE user_" . $me['id'] . " SET data = '$data' WHERE id = '$recordID'";
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
            $result = addRecordToUser($me, $recordData, $me, $connection);
        }
    }
}

function getMessageGroupByID($id, $me, $connection)
{
    $messageGroup = 0;
    if (isPresetGroup($id))
    {
        $messageGroupUsers = array();
        $users = getAllUsers($connection);
        foreach ($users as $user)
        {
            if ($user['id'] != $me['id'])
            {
                if (isMemberOfPresetGroup($id, $user))
                {
                    array_push($messageGroupUsers, $user);
                }
            }
        }
        $messageGroup = array(
            'id' => $id,
            'users' => $messageGroupUsers
        );
    }
    else
    {
        $query = "SELECT * FROM messageGroups WHERE id = '$id'";
        $result = $connection->query($query);
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $messageGroup = packageMessageGroupFromRow($row, $connection);
            }
        }
    }
    return $messageGroup;
}

function getMessageGroupByFootprint($author, $groupTitle, $connection)
{
    $messageGroup = 0;
    $authorID = $author['id'];
    $query = "SELECT * FROM messageGroups WHERE author = '$authorID' AND title = '$groupTitle'";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $messageGroup = packageMessageGroupFromRow($row, $connection);
        }
    }
    return $messageGroup;
}

function getMyMessageGroups($me, $connection)
{
    $messageGroups = 0;
    $query = "SELECT * FROM messageGroups";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $include = 0;
            $messageGroup = packageMessageGroupFromRow($row, $connection);
            if ($messageGroup['author']['id'] == $me['id'])
            {
                $include = 1;
            }
            else
            {
                foreach ($messageGroup['users'] as $user)
                {
                    if ($user['id'] == $me['id'])
                    {
                        $include = 1;
                    }
                }
            }
            if ($include)
            {
                if (!$messageGroups){ $messageGroups = []; }
                array_push($messageGroups, $messageGroup);
            }
        }
    }
    return $messageGroups;
}

function getAllMessageGroups($connection)
{
    $messageGroups = 0;
    $query = "SELECT * FROM messageGroups";
    $result = $connection->query($query);
    if (mysqli_num_rows($result) > 0)
    {
        $messageGroups = array();
        while ($row = mysqli_fetch_array($result))
        {
            $messageGroup = packageMessageGroupFromRow($row, $connection);
            if ($messageGroup)
            {
                array_push($messageGroups, $messageGroup);
            }
        }
        return $messageGroups;
    }
    else
    {
        return 0;
    }
}

function getUnreadMessageCount($me, $connection)
{
    $unreadMessageCount = 0;
    $users = getAllUsersExceptMe($me, $connection);
    foreach ($users as $user)
    {
        $messages = getAllMessagesFromRecipient($me, $user, 'private', $connection);
        if ($messages)
        {
            foreach ($messages as $message)
            {
                if (!$message['isRead'])
                {
                    //debug($message);
                    $unreadMessageCount++;
                }
            }
        }
        
    }
    $unreadMessageCount += getUnreadMessageCountForGroup('all', $me, $connection);
    if (isMemberOfPresetGroup('axoneron', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('axoneron', $me, $connection);
    }
    if (isMemberOfPresetGroup('admin', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('admin', $me, $connection);
    }
    if (isMemberOfPresetGroup('support', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('support', $me, $connection);
    }
    if (isMemberOfPresetGroup('medical', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('medical', $me, $connection);
    }
    if (isMemberOfPresetGroup('commercial', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('commercial', $me, $connection);
    }
    if (isMemberOfPresetGroup('engage', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('engage', $me, $connection);
    }
    if (isMemberOfPresetGroup('pixelmosaic', $me))
    {
        $unreadMessageCount += getUnreadMessageCountForGroup('pixelmosaic', $me, $connection);
    }
    $myGroups = getMyMessageGroups($me, $connection);
    if ($myGroups)
    {
        foreach ($myGroups as $messageGroup)
        {
            $unreadMessageCount += getUnreadMessageCountForGroup($messageGroup['id'], $me, $connection);
        }
    }
    return $unreadMessageCount;
}

function getUnreadMessageCountForGroup($groupID, $me, $connection)
{
    $unreadMessageCount = 0;
    $messageGroup = getMessageGroupByID($groupID, $me, $connection);
    $conversation = getConversation($me, $messageGroup, 'group', $connection);
    if ($conversation)
    {
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
    return $unreadMessageCount;
}

function sendMessage($sender, $connection)
{
    $code = 0;
    $errors = 0;
    $record = 0;
    $recipientID = 0;
    $recipientType = "";
    $messageData = urlencode($_POST['notePadData']);
    $recipient = isset($_POST['recipient']) ? getUserById($_POST['recipient'], $connection) : null;
    if ($recipient != "")
    {
        $recipients = array(getUserById($_POST['recipient'], $connection));
        $recipientID = $_POST['recipient'];
        $groupID = 0;
        $recipientType = "private";
    }
    else if ($_POST['group'] != "")
    {
        $recipients = getMessageGroupByID($_POST['group'], $sender, $connection)['users'];
        $groupMember = 0;
        if ((is_array($recipients)) || (is_object($recipients)))
        {
            foreach ($recipients as $recipient)
            {
                if ($recipient['id'] == $sender['id'])
                {
                    $groupMember = 1;
                }
            }
        }
        $recipientID = $groupID = $_POST['group'];
        $recipientType = "group";
    }
    $recordData = array();
    $recordData['type'] = SENT_MESSAGE;
    $recordData['data'] = $recipientID . "," . $recipientType;
    $recordData['openEnd'] = $messageData;
    if (($groupMember == 1) || ($recipientType == "private"))
    {
        $result = addRecordToUser($sender, $recordData, $sender, $connection);
        if ($result)
        {
            $recordData['type'] = RECEIVED_MESSAGE;
            $recordData['data'] = $sender['id'] . ",0," . $groupID;
            foreach ($recipients as $recipient)
            {
                if ($recipient['id'] != $sender['id'])
                {
                    $result2 = addRecordToUser($sender, $recordData, $recipient, $connection);
                    if ($result2)
                    {
                        $record = getRecordByFootprint($recordData, $recipient, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            // record footprint error
                            $code = 1;
                            $errors = packageGeneralError($errors, 5);
                        }
                    }
                    else
                    {
                        // record not added to user
                        $code = 1;
                        $errors = packageGeneralError($errors, 4);
                    }
                }
            }
            if (!$code)
            {
                $code = -1;
            }
        }
        else
        {
            // record not added to user
            $code = 1;
            $errors = packageGeneralError($errors, 3);
        }
    }
    else
    {
        // user not part of group
        $code = 1;
        $errors = packageGeneralError($errors, 2);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "record" => $record
    );
}

function addMessageGroup($user, $connection)
{   
    $code = 0;
    $errors = 0;
    $messageGroup = 0;
    $record = 0;
    
    $userID = $user['id'];
    $groupTitle = urlencode($_POST['groupTitle']);
    
    $footprintData = array("groupTitle" => urldecode($groupTitle));
    
    $duplicate = checkForDuplicateMessageGroupTitle($footprintData['groupTitle'], $connection);
    if (!$duplicate)
    {
        $groupUsers = $_POST['groupUsers'];
        $query = "INSERT INTO messageGroups (id, author, title, users) VALUES "
                . "(NULL, '$userID', '$groupTitle', '$groupUsers')";
        $result = $connection->query($query);
        if ($result)
        {
            $recordData = array();
            $recordData['type'] = ADDED_MESSAGE_GROUP;
            $recordData['data'] = $messageGroup['id'];
            $recordData['openEnd'] = "";
            $result2 = addRecordToUser($user, $recordData, $user, $connection);
            if ($result2)
            {
                $messageGroup = getMessageGroupByFootprint($user, $groupTitle, $connection);
                if ($messageGroup)
                {
                    $record = getRecordByFootprint($recordData, $user, $connection);
                    if ($record)
                    {
                        $code = -1;
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 9);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 8);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 7);
            }

        }
        else
        {
            $code = 1;
            $errors = packageGeneralError($errors, 6);
        }
    }
    else
    {
        foreach ($duplicate['errors'] as $error)
        {
            switch ($error['code'])
            {
                case 20:
                    $code = 1;
                    $errors = packageGeneralError($errors, 20);
                break;
                case 21:
                    $code = 1;
                    $errors = packageGeneralError($errors, 21);
                break;
            }
        }
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "messageGroup" => $messageGroup,
        "record" => $record
    );
}

function modifyMessageGroup($user, $connection)
{
    $code = 0;
    $errors = 0;
    $messageGroup = 0;
    $messageGroup2 = 0;
    $record = 0;
    
    $userID = $user['id'];
    $groupTitle = urlencode($_POST['groupTitle']);
    $footprintData = array('groupTitle' => urldecode($groupTitle));
    $groupUsers = $_POST['groupUsers'];
    $groupID = $_POST['groupID'];
    $authors = getAllAuthors($connection);
    if ($authors['author']['id'] == $user['id'])
    {
        $duplicate = checkForDuplicateMessageGroupTitle($footprintData['groupTitle'], $connection, $groupID);
        if (!$duplicate)
        {
            $messageGroup = getMessageGroupByID($groupID, $userID, $connection);

            $query = "UPDATE messageGroups SET ";
            $query .= "title = '$groupTitle', ";
            $query .= "users = '$groupUsers' ";
            $query .= " WHERE id = '$groupID'";
            //echo $query . "<br/><br/><br/>";
            $result = $connection->query($query);
            if ($result)
            {
                $messages = 0;
                $allUsers = getAllUsers($connection);
                foreach ($allUsers as $u)
                {
                    $newMessages = getAllMessagesToRecipient($u, $messageGroup, "group", $connection);
                    if ($newMessages)
                    {
                        if (!$messages){ $messages = []; }
                        $messages = array_merge($messages, $newMessages);
                    }
                }
                if ($messages)
                {
                    $groupUsers = explode(",", $groupUsers);
                    foreach($messages as $message)
                    {
                        $author = getUserById($message['author'], $connection);
                        foreach ($groupUsers as $userID)
                        {
                            if ($message['author'] != $userID)
                            {
                                $u = getUserById($userID, $connection);
                                $duplicate = checkForDuplicateMessage($message, $author, $u, $connection);
                                if (!$duplicate)
                                {
                                    $type = RECEIVED_MESSAGE;
                                    $data = $message['author'] . ",0," . $message['messageGroup']['id'];
                                    $openEnd = urlencode($message['message']);
                                    $authorID = $message['author'];
                                    $timestamp = date(getSqlDateFormat(), $message['timeStamp']);

                                    $query = "INSERT INTO user_" . $userID . " (id, type, data, openEnd, entryDate, author) VALUES "
                                        . "(NULL, '$type', '$data', '$openEnd', '$timestamp', '$authorID')";
                                    //echo $query . "<br/><br/><br/>";
                                    $connection->query($query);
                                }
                            }
                        }
                    }
                }
                $recordData = array();
                $recordData['type'] = MODIFIED_MESSAGE_GROUP;
                $recordData['data'] = $groupID;
                $recordData['openEnd'] = "";
                $result2 = addRecordToUser($user, $recordData, $user, $connection);
                if ($result2)
                {
                    $messageGroup2 = getMessageGroupByFootprint($user, $groupTitle, $connection);
                    if ($messageGroup2)
                    {
                        $record = getRecordByFootprint($recordData, $user, $connection);
                        if ($record)
                        {
                            $code = -1;
                        }
                        else
                        {
                            $code = 1;
                            $errors = packageGeneralError($errors, 11);
                        }
                    }
                    else
                    {
                        $code = 1;
                        $errors = packageGeneralError($errors, 10);
                    }
                }
                else
                {
                    $code = 1;
                    $errors = packageGeneralError($errors, 9);
                }
            }
            else
            {
                $code = 1;
                $errors = packageGeneralError($errors, 8);
            }
        }
        else
        {
            foreach ($duplicate['errors'] as $error)
            {
                switch ($error['code'])
                {
                    case 20:
                        $code = 1;
                        $errors = packageGeneralError($errors, 20);
                    break;
                    case 21:
                        $code = 1;
                        $errors = packageGeneralError($errors, 21);
                    break;
                }
            }
        }
    }
    else
    {
        $code = 1;
        $errors = packageGeneralError($errors, 7);
    }
    return array(
        "code" => $code,
        "errors" => $errors,
        "messageGroup" => $messageGroup2,
        "record" => $record
    );
}

function packageMessageDataFromRow($row, $me, $direction, $connection)
{
    $data = explode(",", $row['data']);
    $messageGroup = 0;
    $record = 0;
    $message = urldecode($row["openEnd"]);
    $timeStamp = strtotime($row["entryDate"]);
    if ($direction == SENT_MESSAGE)
    {
        $messageGroup = $data[1] == "group" ? getMessageGroupByID($data[0], $me, $connection) : 0;
        $recipient = $data[1] == "private" ? getUserById($data[0], $connection) : 0;
        $record = array(
            'recordID' => $row['id'],
            'direction' => $direction,
            'recipient' => $recipient,
            'messageGroup' => $messageGroup,
            "message" => $message,
            "author" => $me['id'],
            "timeStamp" => $timeStamp);
    }
    else
    {
        $sender = getUserById($data[0], $connection);
        if ($data[2] && $data[2] != "")
        {
            $messageGroup = getMessageGroupByID($data[2], $me, $connection);
        }
        $record = array(
            'recordID' => $row['id'],
            'direction' => $direction,
            'sender' => $sender,
            'isRead' => $data[1],
            'messageGroup' => $messageGroup,
            "message" => $message,
            "timeStamp" => $timeStamp);
    }
                
    return $record;
}

function packageMessageGroupFromRow($row, $connection)
{
    $messageGroupIDs = explode(",", $row['users']);
    $messageGroupUsers = array();
    $users = getAllUsers($connection);
    foreach ($users as $user)
    {
        $include = 0;
        foreach ($messageGroupIDs as $id)
        {
            if ($id == $user['id'])
            {
                $include = 1;
            }
        }
        if ($include)
        {
            if (!$messageGroupUsers){ $messageGroupUsers = []; }
            array_push($messageGroupUsers, $user);
        }
    }
    
    $author = getUserById($row['author'], $connection);
    array_push($messageGroupUsers, $author);
    
    $request = array(
        'id' => $row['id'],
        'author' => $author,
        'title' => urldecode($row['title']),
        'users' => $messageGroupUsers
    );
    return $request;
}


/**************************************************
 *  TEXT MESSAGE NOTIFICATIONS
 * ************************************************
 */

function sendTextMessageToUsers($message, $users)
{
    if (MESSAGING_ENABLED)
    {
        $phoneNumbers = array();
        foreach ($users as $user)
        {
            $phoneString = validatePhoneNumberForTextMessage($user['phone']);
            if ($phoneString)
            {
                array_push($phoneNumbers, $phoneString);
            }
        }
        if (count($phoneNumbers) > 0)
        {
            $MessageBird = new \MessageBird\Client(MESSAGE_BIRD_KEY);
            $message = new \MessageBird\Objects\Message();
            $message->originator = 'Axoneron App';
            $message->recipients = $phoneNumbers;
            $message->body = $message;
            $MessageBird->messages->create($message);
        }
    }
}

function sendNotification($title, $body, $recipient)
{
    if (MESSAGING_ENABLED && !isLocal())
    {
        $token = "/topics/";
        if (is_numeric($recipient))
        {
            $token .= "user" . $recipient;
        }
        else
        {
            $token .= $recipient;
        }
        $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. FIREBASE_SERVER_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, FIREBASE_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE)
        {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
    }
}

function validatePhoneNumberForTextMessage($phoneNumber)
{
    $valid = 0;
    $phoneString = '+';
    if (strlen($phoneNumber) >= 10)
    {
        if (substr($phoneNumber, 0, 1) == "1")
        {
            if (strlen($phoneNumber) == 11)
            {
                $valid = 1;
                $phoneString .= $phoneNumber;
            }
        }
        else if (strlen($phoneNumber) == 10)
        {
            $valid = 1;
            $phoneString .= "1" . $phoneNumber;
        }
    }
    if (!$valid)
    {
        return 0;
    }
    else
    {
        return $phoneString;
    }
}


/**************************************************
 *  RETURNING HTML BLOCKS FOR REQUESTS (IN GENERAL)
 * ************************************************
 */

/*
 * Produces html for a short format block version display of a request
 */
function getShortRequestBlock($pR, $connection)
{
    $congress = parseCongressFromRecord($pR['userRecord'], $connection);
    $requestDate = getDateTimeDisplayFromTimestamp($pR['userRecord']['timeStamp']);
    if ($pR['userRecord']['type'] == HOTEL_REQUEST || $pR['userRecord']['type'] == MODIFIED_HOTEL_REQUEST)
    {
        $typeDivString = "Hotel";
    }
    else
    {
        $typeDivString = "Meeting Room";
    }
    
    $html = "
    <div class='requestItemDIV'><a href='" . HOME . "?action=" . POST_VIEW_REQUEST . "&request=" . $pR['recordID'] . "'>
        <div class='requestorInfoDIV'>" . $pR['sourceOfRequest']['first'] . " " . $pR['sourceOfRequest']['last'] . "</div>
        <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
        <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
        <div class='requestTypeDIV'>" . $typeDivString . "</div>
        <div class='recordTimeStamp'>" . $requestDate . "</div>
    </a></div>
    ";
    
    return $html;
}


/*****************************************
 *  RETURNING HTML BLOCKS FOR RESERVATIONS
 * ***************************************
 */

/*
 * Produces html for a short format block version display of a reservation
 */
function getShortFormatReservationBlock($record, $connection)
{
    $congress = getCongressById($record['reservation']['congressID'], $connection);
    
    $html = "
    <div class='shortReservationDIV'><a href='" . HOME . "?page=reservations&congress=" . $congress['id'] . "'>
        <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
        <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>";
    
    if ($record['type'] == HOTEL_REQUEST || $record['type'] == MODIFIED_HOTEL_REQUEST)
    {
        $html .= "
        <div class='copyStyle2'>Request Pending</div>";
    }
    else if ($record['type'] == HOTEL_CONFIRMATION)
    {
        $hotel = getHotelById($record['reservation']['hotelID'], $connection);

        $html .= "
        <div class='shortCongressHotel'>" . $hotel['name'] . "</div>";
    }
    $html .= "
    </a></div>";
    
    echo $html;
}


/*
 * Produces html for a long format block version display of a reservation
 */
function getLongFormatReservationBlock($record, $connection)
{
    $html = "
    <div class='longReservationDIV'>";
    
    $congress = getCongressById($record['reservation']['congressID'], $connection);
        
    if ($record['type'] == HOTEL_REQUEST || $record['type'] == MODIFIED_HOTEL_REQUEST)
    {
        
        $html .= "
        <form class='removalForm hotelRezRemovalForm' name='hotelReservationRemove' method='post' onsubmit='confirmHotelRezDelete();' action='" . HOME . "'>
            <div class='trash rezTrash fa'><label for='rezRemove'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='authorID' hidden='true' type='number' value='" . $record['authorID'] . "'/>
            <input id='rezRemove' type='submit' name='" . POST_REMOVE_HOTEL_RESERVATION . "' value=''/>
        </form>
        <a href='" . HOME . "?page=" . POST_REQUEST_HOTEL . "&congress=" . $congress['id'] . "'>
            <form class='editForm hotelRezEditForm'>
                <div class='edit rezEdit fa'><label for='rezEdit'>&#xf044;</label></div>
            </form>
        </a>
        <div class='hotelInfoDIV additionalInfo'>
            <div><span class='titleStyle2'>Check In:</span> " . parseDateFromDateTime($congress['hotelStartDate']) . "</div>
            <div><span class='titleStyle2'>Check Out:</span> " . parseDateFromDateTime($congress['hotelEndDate']) . "</div>
            <div><span class='titleStyle2'>Room Type:</span> " . convertHotelTermForDisplay('roomType', $record['reservation']['roomType']) . "</div>
            <div><span class='titleStyle2'>Occupancy:</span> " . $record['reservation']['occupancy'] . "</div>";
        
        if ($record['openEnd'] != "")
        {
            $html .= "
            <div><span class='titleStyle2'>Special Requests:</span> " . $record['openEnd'] . "</div>";
        }
        
        $html .= "
        </div>
    </div>";
    }
    else if ($record['type'] == HOTEL_CONFIRMATION)
    {
        
        $hotel = getHotelById($record['reservation']['hotelID'], $connection);
        $html .= "
        <form class='removalForm hotelRezRemovalForm' name='hotelReservationRemove' method='post' onsubmit='confirmHotelRezDelete();' action='" . HOME . "'>
            <div class='trash rezTrash fa'><label for='rezRemove'>&#xf1f8;</label></div>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input name='authorID' hidden='true' type='number' value='" . $record['authorID'] . "'/>
            <input id='rezRemove' type='submit' name='" . POST_REMOVE_HOTEL_RESERVATION . "' value=''/>
        </form>
        <a href='" . HOME . "?page=" . POST_REQUEST_HOTEL . "&congress=" . $congress['id'] . "'>
            <form class='editForm hotelRezEditForm'>
                <div class='edit rezEdit fa'><label for='rezEdit'>&#xf044;</label></div>
            </form>
        </a>
        <a href='" . $hotel['url'] . "' target='_blank'>
            <div class='hotelInfoDIV'>
                <div class='titleStyle2'>" . $hotel['name'] . "</div>
                <div>" . $hotel['address1'] . " " . $hotel['address2'] . "</div>
                <div>" . $hotel['city'] . ", " . $hotel['state'] . " " . $hotel['zip'] . "</div>
                <div>" . $hotel['phone'] . "</div>
            </div>
        </a>
        <div class='caret rezCaret fa'>&#xf105;</div>
        <div class='hotelInfoDIV additionalInfo' style='display:none'>
            <div><span class='titleStyle2'>Check In:</span> " . $record['reservation']['checkInDate'] . "</div>
            <div><span class='titleStyle2'>Check Out:</span> " . $record['reservation']['checkOutDate'] . "</div>
            <div><span class='titleStyle2'>Room Type:</span> " . convertHotelTermForDisplay('roomType', $record['reservation']['roomType']) . "</div>
            <div><span class='titleStyle2'>Occupancy:</span> " . $record['reservation']['occupancy'] . "</div>";
        
        if ($record['reservation']['specialRequest'] != "")
        {
            $html .= "
            <div><span class='titleStyle2'>Special Requests:</span> " . $record['reservation']['specialRequest'] . "</div>";
        }
        $html .= "
        </div>
    </div>
    <script>
            $('.rezCaret').click(function()
            {
                if($('.additionalInfo').is(':visible'))
                {
                    $('.additionalInfo').hide();
                    $('.rezCaret').html('&#xf105;');
                }
                else
                {
                    $('.additionalInfo').show();
                    $('.rezCaret').html('&#xf107;');
                }
            });
    </script>";
    }
    return $html;
}

/*****************************************
 *  RETURNING HTML BLOCKS FOR MESSAGES
 * ***************************************
 */

function getShortUserBlock($user)
{
    $html = "
    <div id='shortUser_" . $user['id'] . "' class='conversationDIV2'>";
    
    if ($user['imageURL'])
    {
        $html .= "<img id='image_" . $user['id'] . "' class='accountImage' onload='cleanImage(" . $user['id'] . ");' src='" . USER_IMAGES_PATH . $user['imageURL'] . "' style='display:none;'/>";
    }
    
    $html .= "
        <div id='imageDIV_" . $user['id'] . "' class='shortUserImageDIV'";
    
    if ($user['imageURL'])
    {
        $html .= " style='background-image: url(\"" . USER_IMAGES_PATH . $user['imageURL'] . "\");'";
    }
    
    $html .= ">&nbsp;</div>
        <div class='shortConvoDataDIV1'>
            <div class='shortUserNameDIV'>" . $user['first'] . " " . $user['last'] . "</div>
            <div class='shortUserTitleDIV'>" . $user['title'] . "</div>
        </div>
        <a href='" . HOME . "?page=messages&recipient=" . $user['id'] . "'><div class='messageIcon shortEmailDIV fas'>&#xf086;</div></a>
        <a class='mobilesOnly' href='tel:". $user['phone'] . "'><div class='phoneIcon shortPhoneDIV fas'>&#xf879;</div></a>
        <a href='mailto:" . $user['email'] . "'><div class='emailIcon shortEmailDIV fas'>&#xf0e0;</div></a>
    </div>";
    
    return $html;
}

function getShortUserMessageBlock($me, $user, $connection)
{
    $messageString = 0;
    $unreadMessageCount = 0;
    $messages = getAllMessagesFromRecipient($me, $user, 'private', $connection);
    if ($messages)
    {
        //debug($messages[count($messages) - 1]);
        $messageString = nl2br($messages[count($messages) - 1]['message']);
        foreach ($messages as $message)
        {
            if (!$message['isRead'])
            {
                $unreadMessageCount++;
            }
        }
    }
    if (!$messageString)
    {
        $messageString = "&nbsp;";
    }
    
    $html = "
    <div>
        <a href='" . HOME . "?action=" . ENGAGE_CONVERSATION . "&recipient=" . $user['id'] . "'>
        <div class='shortUserMessageDIV'>";
        
    if ($user['imageURL'])
    {
        $html .= "<img id='image_" . $user['id'] . "' class='accountImage' onload='cleanImage(" . $user['id'] . ");' src='" . USER_IMAGES_PATH . $user['imageURL'] . "' style='display:none;'/>";
    }
    
    $html .= "
            <div id='imageDIV_" . $user['id'] . "' class='shortUserImageDIV'";
    
    if ($user['imageURL'])
    {
        $html .= " style='background-image: url(\"" . USER_IMAGES_PATH . $user['imageURL'] . "\");'";
    }
    
    $html .= ">&nbsp;</div>
            <div class='shortUserDataDIV'>
                <div class='shortUserNameDIV'>" . $user['first'] . " " . $user['last'] . "</div>
                <div class='shortUserMessageDataDIV'>" . $messageString . "</div>
            </div>";
        
    if ($unreadMessageCount)
    {
        $html .=  "
            <div class='shortUserUnreadMessageCount'>" . $unreadMessageCount . "</div>";
    }
    
    $html .= "
        </div>
        </a>
    </div>";
    
    return $html;
}

function getShortUserMessageBlockWithoutLink($user, $selected)
{
    $html = "
    <div id='shortUser_" . $user['id'] . "' class='shortUserMessageSelectDIV";
    
    if ($selected)
    {
        $html .= " selectedForGroup";
    }
    
    $html .= "'>
        <div class='shortUserMessageDIV'>";
    
    if ($user['imageURL'])
    {
        $html .= "<img id='image_" . $user['id'] . "' class='accountImage' onload='cleanImage(" . $user['id'] . ");' src='" . USER_IMAGES_PATH . $user['imageURL'] . "' style='display:none;'/>";
    }
    
    $html .= "
            <div id='imageDIV_" . $user['id'] . "' class='shortUserImageDIV'";
    
    if ($user['imageURL'])
    {
        $html .= " style='background-image: url(\"" . USER_IMAGES_PATH . $user['imageURL'] . "\");'";
    }
    
    $html .= ">&nbsp;</div>
            <div class='shortUserNameDIV'>" . $user['first'] . " " . $user['last'] . "</div>
        </div>
        </a>
    </div>";
    
    return $html;
}

function getShortGroupMessageBlock($me, $messageGroupID, $connection)
{
    $messageString = 0;
    $unreadMessageCount = 0;
    $messageGroup = getMessageGroupByID($messageGroupID, $me, $connection);
    $title = isPresetGroup($messageGroupID) ? getPresetDisplayName($messageGroupID) : $messageGroup['title'];
    $conversation = getConversation($me, $messageGroup, 'group', $connection);
    if ($conversation)
    {
        $lastMessage = $conversation[count($conversation) - 1];
        //debug($lastMessage);
        $source = $lastMessage['direction'] == RECEIVED_MESSAGE ? $lastMessage['sender'] : $me;
        $messageString = substr($source['first'], 0, 1) . " " . $source['last'] . ": ";
        $messageString .= nl2br($lastMessage['message']);
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
    if (!$messageString)
    {
        $messageString = "&nbsp;";
    }
    
    $additionalClass = getPresetClassName($messageGroupID);
    
    $html = "
    <div>
        <a href='" . HOME . "?action=" . ENGAGE_CONVERSATION . "&group=" . $messageGroupID . "'>
        <div class='shortUserMessageDIV'>
            <div class='shortUserImageDIV " . $additionalClass . "'>&nbsp;</div>
            <div class='shortUserDataDIV'>
                <div class='shortUserNameDIV'>" . $title . "</div>
                <div class='shortUserMessageDataDIV'>" . $messageString . "</div>
            </div>";
        
    if ($unreadMessageCount)
    {
        $html .=  "
            <div class='shortUserUnreadMessageCount'>" . $unreadMessageCount . "</div>";
    }
    
    $html .= "
        </div>
        </a>";
    if ($messageGroup['id'] != "all" &&
        $messageGroup['id'] != "axoneron" &&
        $messageGroup['id'] != "admin" &&
        $messageGroup['id'] != "support" &&
        $messageGroup['id'] != "medical" &&
        $messageGroup['id'] != "commercial" &&
        $messageGroup['id'] != "engage" &&
        $messageGroup['id'] != "pixelmosaic")
    {
        if ($messageGroup['author']['id'] == $me['id'])
        {
            $html .= "
            <a class='messageGroupA' href='" . HOME . "?action=" . POST_MODIFY_MESSAGE_GROUP . "&group=" . $messageGroup['id'] . "'>
                <form class='editForm messageGroupEditForm'>
                    <div class='edit messageGroupEdit fa'><label for='messageGroupEdit'>&#xf044;</label></div>
                </form>
            </a>";
        }
    }
    
    
    $html .= "
    </div>";
    
    return $html;
}

function getLongMessageBlock($message)
{
    $class1 = $message['direction'] == SENT_MESSAGE ? "sentMessageDIV1" : "receivedMessageDIV1";
    $class2 = $message['direction'] == SENT_MESSAGE ? "sentMessageDIV2" : "receivedMessageDIV2";
    $html = "
    <div class='" . $class1 . "'>";
    
    if ($message['direction'] == RECEIVED_MESSAGE && $message['messageGroup'])
    {
        $html .= "
        <div class='senderInfoDIV'>" . $message['sender']['first'] . " " . $message['sender']['last'] . ": </div>";
    }
    
    $html .= "
        <div class='" . $class2 . "'>" . nl2br($message['message']) . "</div>
    </div>";
    return $html;
}

function getNewUserReturn($errors, $new_user)
{
    return array(
        "errors" => $errors,
        "new_user" => $new_user
    );
}