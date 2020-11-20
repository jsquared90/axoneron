<?php

$to = $user['email'];
$headers = 'From: no-reply@mycongressapp.com' . "\r\n";

$title = "Axoneron Congress App - Password Reset";

$body =
"Your password has been reset to :
" . $_POST['newPassword'] . "

Please make sure to login with this password and then change it immediately to something else.
";

$body .= getDownloadNotification();

mail($to, $title, $body, $headers);