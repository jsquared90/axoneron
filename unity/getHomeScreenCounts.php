<?php

require_once 'require_files.php';

$connection = connectToDB();
$format = getUnityOutputFormat();

$user = validateUserForUnity($connection, $format);
if ($user)
{
    $unreadMessageCount = getUnreadMessageCount($user, $connection);
    $unviewedCongressCount = getUnviewedCongressCount($user, $connection);
    $unviewedReservationCount = getUnviewedReservationCount($user, $connection);
    
    $homeScreenCounts = array(
      "unreadMessageCount" => $unreadMessageCount,
      "unviewedCongressCount" => $unviewedCongressCount,
      "unviewedReservationCount" => $unviewedReservationCount
    );
    
    echo packageDataForUnity($format, $homeScreenCounts, "homeScreenCounts");
}