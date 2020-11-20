<?php

echo "Hello World from email_test1!";

$to = "jj.myers@pixelmosaic.com";
$headers = 'From: no-reply@mycongressapp.com';
$title = "Test 1 Email";


$body = 
"This is an email for Test #1 from the ACA installation";

mail($to, $title, $body, $headers);

