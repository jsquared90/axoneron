<?php

    $webBuild = 1;

    require_once 'Constants.php';
    require_once 'Database.php';
    require_once 'Functions_Connection.php';
    require_once 'Functions_Login.php';
    require_once 'Functions_User.php';
    require_once 'Functions_Congress.php';
    require_once 'Functions_Hotel.php';
    require_once 'Functions_Hospitality.php';
    require_once 'Functions_Agenda.php';
    require_once 'Functions_Trash.php';
    require_once 'Functions_Error.php';
    require_once 'Functions_Other.php';
    require_once 'Initialization.php';
    //require_once 'Classes_GoodZipArchive.php';
    
    /*
     * Message Bird Service for sending text messages
     */
    require 'messagebird/vendor/autoload.php';
    
    // Test text Message
    
    /*
    $MessageBird = new \MessageBird\Client(MESSAGE_BIRD_KEY);
    $message = new \MessageBird\Objects\Message();
    $message->originator = 'Axoneron App';
    $message->recipients = array('+18479247983','+18152661624');
    $message->body = 'This is a test message from the Axoneron Congress App';
    print_r(json_encode($MessageBird->messages->create($message)));
     * 
     */
    
    // Test push notification
    
    /*
    $url = "https://fcm.googleapis.com/fcm/send";
    $token = "/topics/all";
    $serverKey = 'AAAA2MnBP2A:APA91bFBORGNT3kAj4eRfIMdB4_uS_ZXr_ZCBvMgqwlxF8VLIbLn0jcJdn3_-0T1g1V0ahCfez4mpOki5skPIfrcVahEbnVbEUv8tf92KUNZUspQzqi25EMUSnUZ27BCIHwbLKTu5EAN';
    $title = "Test Title";
    $body = "Test push notification";
    $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
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
    */
    
   //sendNotification(DOMAIN, "Test push notification", "all");
    
    require 'PhpSpreadsheet-master/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Reader\IReader;
    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    use PhpOffice\PhpSpreadsheet\Shared\Date;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
    $connection = connectToDB();
    
    if ($connection)
    {
        initialize($connection);
        $user = 0;
        if (isset($_GET['action']))
        {
            $action = $_GET['action'];
            switch ($action)
            {
                case POST_SIGN_OUT:
                    setcookie('id', 'null',  time() + 1, "/");
                    setcookie('password', 'null',  time() + 1, "/");
                    header("Location: " . HOME);
                    exit();
                    break;
                default:
                    $user = validateUser($connection);
                    break;
            }
        }
        else
        {
            $user = validateUser($connection);
        }
        if ($user)
        {
            // handle anything that has to do pre-headers here
            
            if (isset($_GET['action']))
            {
                switch ($action)
                {
                    case POST_DOWNLOAD_AGENDA:
                        $link = $_GET['link'];
                        header('Content-Type: application/xlsx');
                        header("Content-Disposition: attachment; filename='" . $link);
                        header("Location: " . $link);
                        break;
                    case POST_DOWNLOAD_MANUAL:
                        header('Content-Type: application/pdf');
                        header("Content-Disposition: attachment; filename=Axoneron_Congress_App_User_Guide.pdf");
                        readFile("Axoneron_Congress_App_User_Guide.pdf");
                        break;
                }
                
            }
            if (isset($_POST[POST_SEND_MESSAGE]))
            {
                if ($_POST['recipient'] != "")
                {
                    setcookie('conversation_recipient_' . $_POST['recipient'], 'null',  time() + 1, "/");
                }
                else if ($_POST['group'] != "")
                {
                    setcookie('conversation_group_' . $_POST['group'], 'null',  time() + 1, "/");
                }
            }
            else if (isset($_POST[POST_ADD_GENERAL_INSIGHT]))
            {
                if (isset($_POST['congressID']) &&
                    isset($_POST['itemID']) &&
                    isset($_POST['notePadData']))
                {
                    $identifier = $user['id'] .  "_" . $_POST['congressID'] . "_" . $_POST['itemID'] . "_generalNotes";
                    setcookie($identifier, 'null',  time() + 1, "/");
                }
            }
            else if (isset($_POST[POST_ADD_INSIGHT]))
            {
                if (isset($_POST['congressID']) &&
                        isset($_POST['itemID']) &&
                        isset($_POST['insightPostTitle']))
                {
                    $identifier1 = $user['id'] .  "_" . $_POST['congressID'] . "_" . $_POST['itemID'] . "_insightPostTitle";
                    $identifier1 = str_replace(" ", "_", $identifier1);
                    $identifier2 = $user['id'] .  "_" . $_POST['congressID'] . "_" . $_POST['itemID'] . "_insightPostNotes";
                    $identifier2 = str_replace(" ", "_", $identifier2);
                    setcookie($identifier1, 'null',  time() + 1, "/");
                    setcookie($identifier2, 'null',  time() + 1, "/");
                }
            }
            else if (isset($_POST[POST_MODIFY_INSIGHT]))
            {
                if (isset($_POST['congressID']) &&
                        isset($_POST['itemID']) &&
                        isset($_POST['previousTitle']) &&
                        isset($_POST['insightEditTitle']))
                {
                    $identifier1 = $user['id'] .  "_" . $_POST['congressID'] . "_" . $_POST['itemID'] . "_" .$_POST['previousTitle'];
                    $identifier1 = str_replace(" ", "_", $identifier1);
                    $identifier2 = $user['id'] .  "_" . $_POST['congressID'] . "_" . $_POST['itemID'] . "_" .$_POST['insightEditTitle'];
                    $identifier2 = str_replace(" ", "_", $identifier2);
                    setcookie($identifier1, 'null',  time() + 1, "/");
                    setcookie($identifier2, 'null',  time() + 1, "/");
                }
            }
        }
        else
        {
            if (isset($_GET['action']) && $_GET['action'] == POST_DOWNLOAD_MANUAL)
            {
                header('Content-Type: application/pdf');
                header("Content-Disposition: attachment; filename=Axoneron_Congress_App_User_Guide.pdf");
                readFile("Axoneron_Congress_App_User_Guide.pdf");
            }
        }
    }
    
    

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<html xmlns="http://www.w3.org/1999/xhtml">
    
<head>
    
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<link href="phase2.css" rel="stylesheet" type="text/css" charset="UTF-8"></link>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/v4-shims.css">
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script> -->
<script rc="src/jquery-key-restrictions.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.js"></script>
<script type="text/javascript" src="https://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="js.cookie.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
<script>
    
    try{Typekit.load({ async: true });}catch(e){}
    
    $( function()
    {
      $( ".datepicker" ).datepicker();
    } );
       
</script>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="initial-scale=1.0">

<title>Axoneron Congress Portal</title>

</head>

<body>
    
    <?php
    
    if ($connection)
    {
        if ($user != 0)
        {
            /*
             *  Do any test/research work involving a user here
             */
            
            $cleanUp = cleanUpUserLog($user, $connection);
            
            //debug(isLocal());
            
            if (isset($_GET['success']))
            {
                if (isset($_GET['page']))
                {
                    $action = 'page=' . $_GET['page'];
                    switch ($_GET['page'])
                    {
                        case POST_VIEW_HOSP_BOOKINGS:
                            $action .= "&congress=" . $_GET['congress'] . "&room=" . $_GET['room'];
                            break;
                        case POST_MODIFY_CONGRESS:
                            $action = 'action=' . POST_MODIFY_CONGRESS . '&congress=' . $_GET['congress'];
                            break;
                    }
                    echo getSuccessCodeWithRedirect($action);
                }
                else
                {
                    echo getSuccessCode();
                }
            }
            else if (isset($_GET['page']))
            {
                include_once $_GET['page'] . '.php';
            }
            else if (isset($_GET['action']))
            {
                switch ($action)
                {
                    case ENGAGE_CONVERSATION:
                        setConversionToRead($user, $connection);
                        include_once 'messages.php';
                        break;
                    case POST_ADD_USER:
                        include_once POST_ADD_USER . '.php';
                        break;
                    case POST_ADD_CONGRESS:
                        include_once POST_ADD_CONGRESS . '.php';
                        break;
                    case POST_MODIFY_CONGRESSES:
                        include_once POST_MODIFY_CONGRESSES . '.php';
                        break;
                    case POST_MODIFY_ACCOUNT:
                        include_once 'addUser.php';
                        break;
                    case POST_MODIFY_CONGRESS:
                        $congress = getCongressById($_GET['congress'], $connection);
                        if ($congress)
                        {
                            include_once POST_MODIFY_CONGRESS . '.php';
                        }
                        else
                        {
                            echo getNoResultsCode();
                        }
                        break;
                    case POST_MODIFY_CONGRESS_DETAIL:
                        include_once POST_ADD_CONGRESS . '.php';
                        break;
                    case POST_MODIFY_HOTEL_RESERVATION:
                        include_once POST_REQUEST_HOTEL . '.php';
                        break;
                    case POST_MODIFY_HOTEL:
                        include_once POST_MODIFY_HOTEL . '.php';
                        break;
                    case POST_MODIFY_HOTELS:
                        include_once POST_MODIFY_HOTELS . '.php';
                        break;
                    case POST_MODIFY_MESSAGE_GROUP:
                        include_once POST_ADD_MESSAGE_GROUP . '.php';
                        break;
                    case POST_MODIFY_USER:
                        include_once POST_ADD_USER . '.php';
                        break;
                    case POST_REMOVE_CONGRESS:
                        if ($user['level'] > 1)
                        {
                            if (isset($_GET['congress']))
                            {
                                if (isset($_GET['confirmed']))
                                {
                                    $congressID = $_GET['congress'];
                                    $congress = getCongressById($congressID, $connection);
                                    $queryError = queryError(removeCongress($congress, $user, $connection), POST_REMOVE_CONGRESS);
                                    if ($queryError["code"] >= 0)
                                    {
                                        echo $queryError["html"];
                                    }
                                    else
                                    {
                                        invokeSuccess();
                                    }
                                }
                                else
                                {
                                    include_once POST_REMOVE_CONGRESS . '.php';
                                }
                            }
                            else
                            {
                                include_once POST_REMOVE_CONGRESS . '.php';
                            }
                        }
                        break;
                    case POST_REMOVE_USER:
                        if ($user['level'] > 1)
                        {
                            if (isset($_GET['user']))
                            {
                                if (isset($_GET['confirmed']))
                                {
                                    $userID = $_GET['user'];
                                    $userToRemove = getUserById($userID, $connection);
                                    $queryError = queryError(removeUser($userToRemove, $user, $connection), POST_REMOVE_USER);
                                    if ($queryError["code"] == 0)
                                    {
                                        echo $queryError["html"];
                                    }
                                    else
                                    {
                                        invokeSuccess();
                                    }
                                }
                                else
                                {
                                    include_once POST_REMOVE_USER . '.php';
                                }
                            }
                            else
                            {
                                include_once POST_REMOVE_USER . '.php';
                            }
                        }
                        break;
                    case POST_VIEW_CONGRESS:
                        $attendance = getCongressAttendance($user, $_GET['congress'], $connection);
                        if ($attendance >= 0)
                        {
                            include_once 'viewCongress.php';
                        }
                        else
                        {
                            include_once 'captureCongressAttendance.php';
                        }
                        break;
                    case POST_VIEW_INSIGHTS:
                        include_once 'downloadInsights.php';
                        break;
                    case POST_VIEW_REQUESTS:
                        include_once POST_VIEW_REQUESTS . '.php';
                        break;
                    case POST_VIEW_REQUEST:
                        include_once POST_VIEW_REQUEST . '.php';
                        break;
                    /*
                    case POST_DOWNLOAD_INSIGHTS:
                        if (isset($_GET['link']))
                        {
                            $downloadPath = $_GET['link'];
                            include_once 'downloadInsightLink.php';
                        }
                        break;
                     * 
                     */
                    case PRE_DOWNLOAD_AGENDA:
                        $congress = getCongressById($_GET['congress'], $connection);
                        include_once POST_MODIFY_CONGRESS . ".php";
                        $action = "action=" . POST_DOWNLOAD_AGENDA . "&link=" . $_GET['link'];
                        reDirect($action);
                        break;
                    default:
                        echo getHeaderDiv($user) . "<br/>Unknown Action!<br/>";
                        break;
                }
            }
            
            // POST ADD
            else if (isset($_POST[POST_ADD_AGENDA]))
            {
                $result = addUploadedAgendaToExisting($user, $connection);
                $count = count($result['errors']);
                if ($result['code'] == -1 && $count == 0)
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
                else
                {
                    $queryError = queryError($result, POST_ADD_AGENDA);
                    if ($queryError)
                    {
                        echo $queryError["html"];
                    }
                    else
                    {
                        $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                        reDirect($action);
                    }
                }
            }
            else if (isset($_POST[POST_ADD_CONGRESS]))
            {
                $queryError = queryError(addCongress($user, $connection), POST_ADD_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $congress = getCongressByName(urlencode($_POST['newCongressName']), $connection);
                    include_once 'email_addCongress.php';
                    $action = "page=" . POST_MODIFY_CONGRESS . "&congress=" . $congress['id'];
                    invokeSuccessWithRedirect($action);
                    //include_once POST_MODIFY_CONGRESS . '.php';
                }
            }
            else if (isset($_POST[POST_ADD_GENERAL_INSIGHT]))
            {
                $queryError = queryError(addGeneralInsight($user, $connection), POST_ADD_GENERAL_INSIGHT);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=agenda&congress=" . $_POST["congressID"] . "&item=" . $_POST["itemID"];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_HOSP_ROOM_TO_CONGRESS]))
            {
                $result = addHospitalityRoomToCongress($user, $connection);
                $queryError = queryError($result, POST_ADD_HOSP_ROOM_TO_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $congress = getCongressById($_POST["congressID"], $connection);
                    $room = $result['room'];
                    include_once 'email_addHospRoomToCongress.php';
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_HOTEL]))
            {
                $queryError = queryError(addHotelToDatabase($user, $connection), POST_ADD_HOTEL);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_HOTELS;
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_HOTEL_TO_CONGRESS]))
            {
                $queryError = queryError(addHotelToCongress($user, $connection), POST_ADD_HOTEL_TO_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }else if (isset($_POST[POST_ADD_INSIGHT]))
            {
                $queryError = queryError(addInsightPost($user, $connection), POST_ADD_INSIGHT);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=agenda&congress=" . $_POST["congressID"] . "&item=" . $_POST["itemID"];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_INSIGHTS]))
            {
                $queryError = queryError(createInsightsDirectory($user, $connection), POST_ADD_INSIGHTS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=agenda&congress=" . $_POST["congressID"] . "&item=" . $_POST["itemID"];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_MESSAGE_GROUP]))
            {
                $queryError = queryError(addMessageGroup($user, $connection), POST_ADD_MESSAGE_GROUP);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=messages";
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_SPEAKER_BIO]))
            {
                $queryError = queryError(addBioToCongress($user, $connection), POST_ADD_SPEAKER_BIO);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_ADD_USER]))
            {
                $queryError = queryError(preregisterUser($user, $connection), POST_ADD_USER);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    include_once 'email_preRegisterUser.php';
                    invokeSuccess();
                }
            }
            
            // POST B
            else if (isset($_POST[POST_BOOK_HOSP_ROOM]))
            {
                $return = bookHospitalityRoom($user, $connection);
                $queryError = queryError($return["code"], POST_BOOK_HOSP_ROOM);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    $congress = getCongressById($_POST["congressID"], $connection);
                    include_once 'email_hospRoomBookingConfirmation.php';
                    $action = 'page=hospitalityBooking&congress='. $_POST["congressID"] . '&room=' . $_POST["roomID"];
                    invokeSuccessWithRedirect($action);
                }
            }
            
            // POST C
            else if (isset($_POST[POST_CONFIRM_HOSP_REQUEST]))
            {
                $return = confirmHospitalityRequest($user, $connection);
                $queryError = queryError($return["code"], POST_CONFIRM_HOSP_REQUEST);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    $congress = getCongressById($_POST["congressID"], $connection);
                    include_once 'email_hospRequestConfirmation.php';
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_CONFIRM_HOTEL]))
            {
                $return = confirmHotelRequest($user, $connection);
                $queryError = queryError($return["code"], POST_CONFIRM_HOTEL);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    include_once 'email_userHotelConfirmationConfirmation.php';
                    invokeSuccess();
                }
            }
            
            // POST D
            else if (isset($_POST[POST_DOWNLOAD_AGENDA]))
            {
                $return = downloadCurrentAgenda($user, $connection);
                $queryError = queryError($return, POST_DOWNLOAD_AGENDA);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . PRE_DOWNLOAD_AGENDA . "&link=" . $return['downloadPath'] . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            
            // POST MODIFY
            else if (isset($_POST[POST_MODIFY_ACCOUNT]))
            {
                $queryError = queryError(modifyAccount($user, $connection), POST_MODIFY_ACCOUNT);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_MODIFY_AGENDA_ITEM]))
            {
                $queryError = queryError(modifyAgendaItem($user, $connection), POST_MODIFY_AGENDA_ITEM);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=" . POST_MODIFY_AGENDA_ITEM . "&congress=" . $_POST['congressID'] . "&item=" . $_POST['itemID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_BIO]))
            {
                $queryError = queryError(modifyBio($user, $connection), POST_MODIFY_BIO);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_CONGRESS_DETAIL]))
            {
                $queryError = queryError(modifyCongress($user, $connection), POST_MODIFY_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['id'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_HOSP_BOOKING]))
            {
                $return = modifyHospitalityRoomBooking($user, $connection);
                $queryError = queryError($return["code"], POST_MODIFY_HOSP_BOOKING);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    $congress = getCongressById($_POST["congressID"], $connection);
                    include_once 'email_hospRoomBookingConfirmation.php';
                    $action = 'page=hospitalityBooking&congress='. $_POST["congressID"] . '&room=' . $_POST["roomID"];
                    invokeSuccessWithRedirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_HOSP_ROOM]))
            {
                $return = modifyHospitalityRoom($user, $connection);
                $queryError = queryError($return["code"], POST_MODIFY_HOSP_ROOM);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    if ($return['affectedBookings'])
                    {
                        include_once 'email_hospRoomForcedReBooking.php';
                    }
                    $action = "page=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    invokeSuccessWithRedirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_HOTEL]))
            {
                $queryError = queryError(modifyHotelDetail($user, $connection), POST_MODIFY_HOTEL);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=" . POST_MODIFY_HOTELS;
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_HOTEL_RESERVATION]))
            {
                $return = modifyHotelRequest($user, $connection);
                $queryError = queryError($return["code"], POST_REQUEST_HOTEL);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    include_once 'email_userHotelRequestConfirmation.php';
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_MODIFY_INSIGHT]))
            {
                $queryError = queryError(editInsightPost($user, $connection), POST_MODIFY_INSIGHT);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=agenda&congress=" . $_POST["congressID"] . "&item=" . $_POST["itemID"];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_MESSAGE_GROUP]))
            {
                $queryError = queryError(modifyMessageGroup($user, $connection), POST_MODIFY_MESSAGE_GROUP);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=messages";
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_MODIFY_USER]))
            {
                modifyUser($user, $connection);
                $queryError = queryError(modifyUser($user, $connection), POST_MODIFY_USER);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    // may need a notification here
                    invokeSuccess();
                }
            }
            
            // POST R
            else if (isset($_POST[POST_REGISTER_USER]))
            {
                $queryError = queryError(registerUser($user, $connection), POST_REGISTER_USER);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    include_once 'email_userRegsisterConfirmation.php';
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_REMOVE_HOSP_BOOKING]))
            {
                $congressID = $_POST['congressID'];
                $bookingID = $_POST['bookingID'];
                $booking = getHospitalityBookingByID($bookingID, $congressID, $connection);
                $queryError = queryError(removeHospitalityBooking($congressID, $bookingID, $user, $connection), POST_REMOVE_HOSP_BOOKING);
                if ($queryError["code"] == 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    include_once 'email_hospRoomBookingCancellation.php';
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_REMOVE_HOTEL_RESERVATION]))
            {
                $author = getUserById($_POST['authorID'], $connection);
                $congressID = $_POST['congressID'];
                $queryError = queryError(removeHotelRequest($author, $congressID, $user, $connection), POST_REMOVE_HOTEL_RESERVATION);
                if ($queryError["code"] == 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    include_once 'email_userHotelCancellation.php';
                    invokeSuccess();
                }
            }
            else if (isset($_POST[POST_REMOVE_INSIGHT]))
            {
                $queryError = queryError(deleteInsightPost($user, $connection), POST_REMOVE_INSIGHT);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=agenda&congress=" . $_POST["congressID"] . "&item=" . $_POST["itemID"];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS]))
            {
                $queryError = queryError(deleteAgendaItem($user, $connection), POST_REMOVE_AGENDA_ITEM_FROM_CONGRESS);
                if ($queryError["code"] == 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_REMOVE_BIO]))
            {
                $queryError = queryError(removeBioFromCongress($user, $connection), POST_REMOVE_BIO);
                if ($queryError["code"] == 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_REMOVE_HOSP_ROOM_FROM_CONGRESS]))
            {
                $return = removeHospitalityRoomFromCongress($user, $connection);
                $queryError = queryError($return['code'], POST_REMOVE_HOSP_ROOM_FROM_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    if ($return['affectedBookings'])
                    {
                        $affectedBookings = $return['affectedBookings'];
                        include_once 'email_hospRoomCancellation.php';
                    }
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_REMOVE_HOTEL_FROM_CONGRESS]))
            {
                $queryError = queryError(removeHotelFromCongress($user, $connection), POST_REMOVE_HOTEL_FROM_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    // might need a notification here
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_REPLACE_AGENDA]))
            {
                $result = replaceAgenda($user, $connection);
                $count = count($result['errors']);
                if ($result['code'] == -1 && $count == 0)
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
                else
                {
                    $queryError = queryError($result, POST_REPLACE_AGENDA);
                    if ($queryError)
                    {
                        echo $queryError["html"];
                    }
                    else
                    {
                        $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                        reDirect($action);
                    }
                }
            }
            else if (isset($_POST[POST_REQUEST_HOTEL]))
            {
                $return = submitHotelRequest($user, $connection);
                $queryError = queryError($return["code"], POST_REQUEST_HOTEL);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $record = $return["record"];
                    include_once 'email_userHotelRequestConfirmation.php';
                    invokeSuccess();
                }
            }
            
            // POST S
            else if (isset($_POST[POST_SEND_MESSAGE]))
            {
                $queryError = queryError(sendMessage($user, $connection), POST_SEND_MESSAGE);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    if ($_POST['recipient'] != "")
                    {
                        $action = "page=messages&recipient=" . $_POST["recipient"];
                    }
                    else if ($_POST['group'] != "")
                    {
                        $action = "page=messages&group=" . $_POST["group"];
                    }
                    reDirect($action);
                }
            }
            
            // POST U
            else if (isset($_POST[POST_UPLOAD_AGENDA]))
            {
                $result = addUploadedAgendaToCongress($user, $connection);
                $count = count($result['errors']);
                if ($result['code'] == -1 && $count == 0)
                {
                    $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                    reDirect($action);
                }
                else
                {
                    $queryError = queryError($result, POST_UPLOAD_AGENDA);
                    if ($queryError)
                    {
                        echo $queryError["html"];
                    }
                    else
                    {
                        $action = "action=" . POST_MODIFY_CONGRESS . "&congress=" . $_POST['congressID'];
                        reDirect($action);
                    }
                }
            }
            
            // POST V
            else if (isset($_POST[POST_VALIDATE_EMAIL]))
            {
                if ($user['password'] == '')
                {
                    include_once 'userRegistration.php';
                }
                else
                {
                    include_once 'loginFormStep2.php';
                }
            }
            else if (isset($_POST[POST_VIEW_CONGRESS]))
            {
                $queryError = queryError(setCongressAttendance($user, $connection), POST_VIEW_CONGRESS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "page=" . POST_VIEW_CONGRESS . "&congress=" . $_POST['congress'];
                    reDirect($action);
                }
            }
            else if (isset($_POST[POST_VIEW_INSIGHTS]))
            {
                $return = getAllInsightsForCongress($user, $connection);
                $queryError = queryError($return, POST_VIEW_INSIGHTS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $userInsightPackages = $return['insights'];
                    include_once 'viewInsights.php';
                }
            }
            
            
            
            
            
            
            
            
            /*
            else if (isset($_POST[POST_DOWNLOAD_INSIGHTS]))
            {
                $return = packageInsights($user, $connection);
                $queryError = queryError($return, POST_DOWNLOAD_INSIGHTS);
                if ($queryError["code"] >= 0)
                {
                    echo $queryError["html"];
                }
                else
                {
                    $action = "action=" . POST_DOWNLOAD_INSIGHTS . "&link=" . $return['downloadPath'];
                    reDirect($action);
                }
            }
             * 
             */
            else
            {
                include_once 'home.php';
            }
        }
        else
        {
            include_once 'loginFormStep1.php';
        }

        closeConnection($connection);
    }
    else
    {
        echo "Connection failed.";
    }
    
    
    ?>
    
    <!-- all Firebase stuff below is for future use. It is for if we decide to handle push notifications
    on the web build -->
    
    <!-- The core Firebase JS SDK is always required and must be listed first -->
<!-- <script src="https://www.gstatic.com/firebasejs/7.15.1/firebase-app.js"></script> -->

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<!-- <script src="https://www.gstatic.com/firebasejs/7.15.1/firebase-messaging.js"></script> -->

<script>
    
    // Your web app's Firebase configuration
  
    /*
    var firebaseConfig =
    {
        apiKey: "AIzaSyAA6lr6uP282QAPoST3sb2OLHL5YZwsF88",
        authDomain: "axoneron-33184.firebaseapp.com",
        databaseURL: "https://axoneron-33184.firebaseio.com",
        projectId: "axoneron-33184",
        storageBucket: "axoneron-33184.appspot.com",
        messagingSenderId: "931097821024",
        appId: "1:931097821024:web:f32dc2ec64f765415ca805",
        measurementId: "G-ZCWE24K6TC"
    };
        
    // Initialize Firebase
  
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    messaging.usePublicVapidKey("BMuIiufDcZH-mYIg8eXPHra8hFyGmRhcsGapKdprhCLYuKnVx5fr-ZXjPt6Yh8G0VcMGsySUdyErAu3PRZgYrEw");
    
    // Get Instance ID token. Initially this makes a network call, once retrieved
    // subsequent calls to getToken will return from cache.
    messaging.getToken().then((currentToken) =>
    {
      if (currentToken)
      {
        sendTokenToServer(currentToken);
        updateUIForPushEnabled(currentToken);
      }
      else
      {
        // Show permission request.
        console.log('No Instance ID token available. Request permission to generate one.');
        // Show permission UI.
        updateUIForPushPermissionRequired();
        setTokenSentToServer(false);
      }
    }).catch((err) =>
    {
      console.log('An error occurred while retrieving token. ', err);
      showToken('Error retrieving Instance ID token. ', err);
      setTokenSentToServer(false);
    });
    
    // Callback fired if Instance ID token is updated.
    messaging.onTokenRefresh(() =>
    {
      messaging.getToken().then((refreshedToken) =>
      {
        console.log('Token refreshed.');
        // Indicate that the new Instance ID token has not yet been sent to the
        // app server.
        setTokenSentToServer(false);
        // Send Instance ID token to app server.
        sendTokenToServer(refreshedToken);
        // ...
      }).catch((err) =>
      {
        console.log('Unable to retrieve refreshed token ', err);
        showToken('Unable to retrieve refreshed token ', err);
      });
    });
     * 
     */
    
</script>

</body>
</html>
