<?php

$to = $_POST['newUserEmail'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$headers .= "CC: " . ADMIN_EMAIL_GROUP;
if ($_POST['newUserCC'] == "Yes")
{
    {
        $headers .= "," . $user['email'];
    }
    //$from = "From: no-reply@mycongressapp.com";
    $title = "Axoneron Congress App Registration";
    $href = 'http://' . DOMAIN . '?email=' . $to;

    $body =
    "You have been pre-registered for the Axoneron Congress App. Please complete your registration by clicking this link:
    
    ";

    $body .= $href;
    
    $body .= getDownloadNotification();

    mail($to, $title, $body, $headers);
}
else
{
    return 0;
}