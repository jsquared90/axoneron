<?php

$reservation = $record["reservation"];
$congress = getCongressById($reservation['congressID'], $connection);
$checkInDate = parseDateFromDateTime($congress["hotelStartDate"]);
$checkOutDate = parseDateFromDateTime($congress["hotelEndDate"]);

$to = $user['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
$title = "Hotel request for " . $congress['shortName'];


$body = 
"Your hotel request for " . $congress['name'] . " has been submitted. An administrator will receive your request and follow up with a confirmation. Here are the details of your request:

Record Identifier : " . $record['id'] . "
Check In : " . $checkInDate . "
Check Out : " . $checkOutDate . "
Room Type : " . convertHotelTermForDisplay('roomType', $reservation['roomType']) . "
Occupancy : " . $reservation['occupancy'] . "
Special Requests : " . $record['openEnd'] . "
        
 ";

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

$notificationBody = "Your hotel request for " . $congress['shortName'] . " has been submitted. Log into the app to see the details of your request.";
sendNotification($title, $notificationBody, $user['id']);
$notificationBody = $user['first'] . " " . $user['last'] . " has made a hotel request for " . $congress['shortName'] . ".";
sendNotification($title, $notificationBody, 'admin');