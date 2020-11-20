<?php

$congress = getCongressById($_POST['congressID'], $connection);
$author = getUserById($_POST["authorID"], $connection);

$to = $author['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
$title = "Hotel cancellation for " . $congress['shortName'];


$body = 
"Your hotel request for " . $congress['name'] . " has been cancelled.";

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

$notificationBody = "Your hotel request for " . $congress['name'] . " has been cancelled.";
sendNotification($title, $notificationBody, $user['id']);
$notificationBody = "The hotel request for " . $user['first'] . " " . $user['last'] . " at " . $congress['shortName'] . " has been cancelled.";
sendNotification($title, $notificationBody, 'admin');