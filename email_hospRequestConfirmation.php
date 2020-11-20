<?php

$booking = $record['booking'];

$recipient = getUserById($record["sourceID"], $connection);
$to = $recipient['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP . "," . getUserById($record["authorID"], $connection)['email'];
$title = "Special Request Confirmation for " . $congress['shortName'];

$body = 
"Your special request for " . $congress['shortName'] . " has been received and reviewed. Here is the response from an administrator :

Booking Record Identifier : " . $booking['id'] . "

Congress : " . $congress['shortName'] . "
Room Name : " . $booking['room']['name'] . "
Booking Name : " . $booking['bookingName'] . "
Date/Time : " . format1ForSingleDateTimeDisplay($booking['date'], $booking['startTime'], $booking['endTime']) . "

Original Request : 
" . $booking['openEnd'] . "
    
Comments from Administrator : 
" . $record['openEnd'] . "
        
 ";

$body .= getDownloadNotification();

//rtrim(date("g:ia", $start), "m")

mail($to, $title, $body, $headers);

//echo $body;

$notificationBody = "Your special request for " . $congress['shortName'] . " has been received and reviewed. Log into the app to see the details of the response.";
sendNotification($title, $notificationBody, $recipient['id']);
$notificationBody = "The special request for " . $recipient['first'] . " " . $recipient['last'] . " at " . $congress['shortName'] . " has been reviewed and handled.";
sendNotification($title, $notificationBody, 'admin');