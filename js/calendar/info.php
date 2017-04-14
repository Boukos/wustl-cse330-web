<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
session_start();

echo json_encode(array(
		"username" => $_SESSION['username'],
		"token" => $_SESSION['token']
	));
exit; 
?>