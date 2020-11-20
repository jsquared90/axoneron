<?php

$title = "Axoneron Congress App";
$notificationBody = "A meeting room named '" . $room['name'] . "' has been added to " . $congress['shortName'] . ". Log in to view availability and to book a time slot.";
sendNotification($title, $notificationBody, 'all');

