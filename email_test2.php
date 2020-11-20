<?php

$to = "admin@mycongressapp.com";
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";
$title = "Test 2 Email";


$body = 
"This is an email for Test Script #2. Script = " . $_POST['script'];

mail($to, $title, $body, $headers);

