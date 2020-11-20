<?php

$to = "admin@mycongressapp.com";
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$title = "Test 3 Email";


$body = 
"This is an email for Test Script #3. Script = " . $_POST['script'];

mail($to, $title, $body, $headers);

