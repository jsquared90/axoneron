<?php

$congress = getCongressById($_POST['congressID'], $connection);
$room = getHospitalityRoomByID($_POST['hospRoomID'], $_POST['congressID'], $connection, 1);

foreach ($affectedBookings as $booking)
{
    //$bCongress = getCongressById($booking['room']['congressID'], $connection);
    $to = $booking['author']['email'];
    $headers = 'From: no-reply@mycongressapp.com' . "\r\n";
    $headers .= "CC: " . ADMIN_EMAIL_GROUP;
    $title = "Meeting Room Schedule Alert for " . $congress['shortName'];
    $body = "
We regret to inform you that meeting room '" . $room['name'] . "' for " . $congress['shortName'] . " has been cancelled, and an exisiting reservation you have has been cancelled as a result:

Booking Record Identifier : " . $booking['id'] . "

Congress : " . $congress['shortName'] . "
Room Name : " . $room['name'] . "
Booking Name : " . $booking['bookingName'] . "
Date/Time : " . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "
Special Request : 
" . $booking['openEnd'] . "

";
    
    $body .= getDownloadNotification();
    
    mail($to, $title, $body, $headers);
    //echo nl2br($body) . "<br/><br/><br/>";
    
    $notificationBody = "We regret to inform you that meeting room '" . $room['name'] . "' for " . $congress['shortName'] . " has been cancelled and has affected a booking of yours.";
    sendNotification($title, $notificationBody, $booking['author']['id']);
}

$notificationBody = "The meeting room '" . $room['name'] . "' at " . $congress['shortName'] . " has been cancelled.";
sendNotification($title, $notificationBody, 'admin');