<?php

$title = "Axoneron Congress App";
$notificationBody = "The congress '" . $congress['shortName'] . "' has been added to the Axoneron Congress App. Please log into the app and confirm your attendance.";
sendNotification($title, $notificationBody, 'all');

