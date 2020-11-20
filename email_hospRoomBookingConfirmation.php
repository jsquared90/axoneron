<?php

$booking = $record['booking'];

$to = $user['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
$title = "Meeting Room Booking Confirmation for " . $congress['shortName'];

$body = 
"Here are the details of your meeting room booking:

Booking Record Identifier : " . $booking['id'] . "

Congress : " . $congress['shortName'] . "
Room Name : " . $booking['room']['name'] . "
Booking Name : " . $booking['bookingName'] . "
Date/Time : " . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "

Special Request : 
" . $booking['openEnd'] . "
        
 ";

//rtrim(date("g:ia", $start), "m")

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

//echo $body;

$notificationBody = "'" . $booking['bookingName'] . "' for the room '" . $booking['room']['name'] . "' at " . $congress['shortName'] . " is confirmed. Log into the app to see the details of your booking.";
sendNotification($title, $notificationBody, $user['id']);
if ($booking['openEnd'] != '')
{
    $notificationBody = $user['first'] . " " . $user['last'] . " has booked '" . $booking['room']['name'] . "' at " . $congress['name'] . " with a special request.";
    sendNotification($title, $notificationBody, 'admin');
}

