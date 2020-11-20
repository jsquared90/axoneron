<?php

$reservation = $record["reservation"];
$congress = getCongressById($reservation['congressID'], $connection);
$hotel = getHotelById($reservation['hotelID'], $connection);

$recipient = getUserById($record["sourceID"], $connection);
$to = $recipient['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP . "," . getUserById($record["authorID"], $connection)['email'];
$title = "Hotel confirmation for " . $congress['shortName'];


$body = 
"Your hotel request for " . $congress['name'] . " has been confirmed. Here are the details for your reservation:

Congress App Record Identifier : " . $record['id'] . "

Hotel : " . $hotel['name'] . "
" . $hotel['address1'] . " " . $hotel['address2'] . "
" . $hotel['city'] . ", " . $hotel['state'] . " " . $hotel['zip'] . "
" . $hotel['phone'] . "

Check In : " . $reservation['checkInDate'] . "
Check Out : " . $reservation['checkOutDate'] . "
Room Type : " . convertHotelTermForDisplay('roomType', $reservation['roomType']) . "
Occupancy : " . $reservation['occupancy'] . "
Confirmation # : " . $reservation['confirmationNumber'] . "

Comments from Administrator : 
" . $record['openEnd'] . "
        
 ";

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

$notificationBody = "Your hotel request for " . $congress['shortName'] . " has been confirmed. Log into the app to see the details of your reservation.";
sendNotification($title, $notificationBody, $recipient['id']);
$notificationBody = "The hotel request for " . $recipient['first'] . " " . $recipient['last'] . " at " . $congress['shortName'] . " has been completed and confirmed.";
sendNotification($title, $notificationBody, 'admin');