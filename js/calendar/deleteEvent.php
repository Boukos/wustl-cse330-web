<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);

// Content of database.php 
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calendar');
if($mysqli->connect_errno) {
	echo json_encode(array(
		"success" => false,
		"message" => "Connection Failed: %s\n", $mysqli->connect_error
	));
	exit;
}

// CSRF
session_start();
if($_SESSION['token'] !== $_POST['token']){ 
	echo json_encode(array(
		"success" => false,
		"message" => "Request forgery detected"
	));
	die();
}

$id= $_POST['event_id'];

$stmt = $mysqli->prepare("DELETE FROM events WHERE events.event_id=?");
if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}

$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->close();

echo json_encode(array(
	"success" => true,
	"message" => "Delete event success"
));
?>