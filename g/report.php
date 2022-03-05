<?php

if (isset($_POST['email']) && isset($_POST['msg'])) {
	$sender = $_POST['email'];
	$msg = $_POST['msg'];
	if ($sender == '') { die(); }
	if ($msg == '') { die(); }
	$message = 'Msg from: ' . $sender . '.' . "\r\n\r\n" . $msg;
	mail('projects@mrgeorgeous.com', 'HighOlymp: Report', wordwrap($message, 70, "\r\n"));
} else {
	header('HTTP/1.0 404 Not Found');
}

?>