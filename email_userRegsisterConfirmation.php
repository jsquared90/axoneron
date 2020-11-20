<?php

$to = $user['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
$title = "Axoneron Congress App Registration";

$body = 
"Congratulations. Your registration with the Axoneron Congress App is complete. Here are your registration details:

First : " . $user['first'] . "
Last : " . $user['last'] . "
Phone : " . $user['phone'] . "
Title : " . $user['title'] . "
Role : " . getUserRole($user) . "
        
 ";
    
$body .= getDownloadNotification();

mail($to, $title, $body, $headers);

$notificationBody = "Your registration with the Axoneron Congress App is complete.";
sendNotification($title, $notificationBody, $user['id']);
$notificationBody = "The registration for " . $user['first'] . " " . $user['last'] . " is complete.";
sendNotification($title, $notificationBody, 'admin');