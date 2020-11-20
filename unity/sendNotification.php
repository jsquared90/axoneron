<?php

require_once 'require_files.php';

$connection = connectToDB();
$user = validateUserForUnity($connection, $format);
$script = $_POST['script'];
if ($user)
{
    switch ($script)
    {
        case POST_ADD_CONGRESS . ".php":
            $congress = getCongressById($_POST["congressID"], $connection);
            include_once '../email_addCongress.php';
            break;
        
        case POST_ADD_HOSP_ROOM_TO_CONGRESS . ".php":
            $congress = getCongressById($_POST["congressID"], $connection);
            $room = getHospitalityRoomByID($_POST['roomID'], $congress['id'], $connection);
            include_once '../email_addHospRoomToCongress.php';
            break;
        
        case POST_MODIFY_HOSP_BOOKING . ".php":
            $record = getRecordByID($_POST['record'], $user, $connection);
            $congress = getCongressById($_POST["congressID"], $connection);
            include_once '../email_hospRoomBookingConfirmation.php';
            break;
        
        case POST_MODIFY_HOSP_ROOM . ".php":
            $congressID = $_POST["congressID"];
            $affectedBookings = array();
            $bookingIDs = explode(",", $_POST['bookingIDs']);
            foreach ($bookingIDs as $bookingID)
            {
                $booking = getHospitalityBookingByID($bookingID, $congressID, $connection, true);
                array_push($affectedBookings, $booking);
            }
            $return = array(
              "affectedBookings" => $affectedBookings  
            );
            include_once '../email_hospRoomForcedReBooking.php';
            break;
        
        case POST_BOOK_HOSP_ROOM . ".php":
            $record = getRecordByID($_POST['record'], $user, $connection);
            $congress = getCongressById($_POST["congressID"], $connection);
            include_once '../email_hospRoomBookingConfirmation.php';
            break;
        
        case POST_CONFIRM_HOSP_REQUEST . ".php":
            $sourceOfRequest = getUserById($_POST['sourceOfRequest'], $connection);
            $record = getRecordByID($_POST['record'], $sourceOfRequest, $connection);
            $congress = getCongressById($_POST["congressID"], $connection);
            include_once '../email_hospRequestConfirmation.php';
            break;
        
        case POST_CONFIRM_HOTEL . ".php":
            $sourceOfRequest = getUserById($_POST['sourceOfRequest'], $connection);
            $record = getRecordByID($_POST['record'], $sourceOfRequest, $connection);
            include_once '../email_userHotelConfirmationConfirmation.php';
            break;
        
        case POST_MODIFY_HOTEL_RESERVATION . ".php":
            $record = getRecordByID($_POST['record'], $user, $connection);
            $reservation = $record['reservation'];
            include_once '../email_userHotelRequestConfirmation.php';
            break;
        
        case POST_REQUEST_HOTEL . ".php":
            $record = getRecordByID($_POST['record'], $user, $connection);
            $reservation = $record['reservation'];
            include_once '../email_userHotelRequestConfirmation.php';
            break;
        
        case POST_REMOVE_HOSP_BOOKING . ".php":
            $record = getRecordByID($_POST['record'], $user, $connection);
            $booking = $record['booking'];
            $congress = getCongressById($_POST["congressID"], $connection);
            include_once '../email_hospRoomBookingCancellation.php';
            break;
        
        case POST_REMOVE_HOSP_ROOM_FROM_CONGRESS . ".php":
            $affectedBookings = getAllHospitalityBookingsForRoom($_POST['congressID'], $_POST['hospRoomID'], $connection, 1);
            include_once '../email_hospRoomCancellation.php';
            break;
        
        case POST_REMOVE_HOTEL_RESERVATION + ".php":
            include_once '../email_userHotelCancellation.php';
            break;
        
        default :
            include_once '../email_test2.php';
            break;
    }
}
else
{
    switch ($script)
    {
        
        case POST_RESET_FORGOTTEN_PASSWORD . ".php":
            $userID = $_POST['id'];
            $user = getUserById($userID, $connection);
            //include_once '../email_test2.php';
            include_once '../email_resetPassword.php';
            break;
    }
}