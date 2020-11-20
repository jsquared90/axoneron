<?php

$congress = getCongressById($_POST['congressID'], $connection);

$to = $user['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
$title = "Meeting Room Booking Cancellation for " . $congress['shortName'];

$body = 
"The following meeting room booking has been cancelled:

Booking Record Identifier : " . $booking['id'] . "

Congress : " . $congress['shortName'] . "
Room Name : " . $booking['room']['name'] . "
Booking Name : " . $booking['bookingName'] . "
Date/Time : " . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "

Special Request : 
" . $booking['openEnd'] . "
        
 ";

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

$notificationBody = "A meeting room booking of yours for " . $congress['shortName'] . " has been cancelled.";
sendNotification($title, $notificationBody, $user['id']);