<?php

foreach ($return['affectedBookings'] as $reBooking)
{
    
    $congress = getCongressById($reBooking['room']['congressID'], $connection);
    $forcedCancellation = $reBooking['startTime'] ? 0 : 1;
    
    $to = $reBooking['author']['email'];
    $headers = 'From: no-reply@mycongressapp.com' . "\r\n";
    $headers .= "CC: " . ADMIN_EMAIL_GROUP;
    $title = "Meeting Room Schedule Alert for " . $congress['shortName'];
    $body = "
We regret to inform you that a change in meeting room schedule/availability has affected an exisiting reservation you have for " . $congress['shortName'] . ".";
    
    if ($forcedCancellation)
    {
        $body .= " Due to the change in meeting room schedule/availability, the following reservation has been cancelled:";
    }
    else
    {
        $body .= " Due to the change in meeting room schedule/availability, the following reservation has been adjusted to the following:";
    }
    
    $body .= "

Booking Record Identifier : " . $reBooking['id'] . "

Congress : " . $congress['shortName'] . "
Room Name : " . $reBooking['room']['name'] . "
Booking Name : " . $reBooking['bookingName'];
    
    if ($forcedCancellation)
    {
        $body .= "
Date/Time : " . format1ForSingleDateTimeDisplay($reBooking['date'], $reBooking['originalStartTime'], $reBooking['originalEndTime']);
    }
    else
    {
        $body .= "
Date/Time : " . format1ForSingleDateTimeDisplay($reBooking['date'], $reBooking['startTime'], $reBooking['endTime']);
    }
    
    $body .= "
Special Request : 
" . $reBooking['openEnd'] . "

";
    
    $body .= getDownloadNotification();
    
    mail($to, $title, $body, $headers);
    //echo nl2br($body) . "<br/><br/><br/>";
    
    $notificationBody = "We regret to inform you that the availability schedule for meeting room '" . $reBooking['room']['name'] . "' at " . $congress['shortName'] . " has been changed and has affected a booking of yours. Please log in to review.";
    sendNotification($title, $notificationBody, $booking['author']['id']);
}

$notificationBody = "The availability schedule for meeting room '" . $booking['room']['name'] . "' at " . $congress['shortName'] . " has been changed";
sendNotification($title, $notificationBody, 'admin');