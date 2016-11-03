<?php
if(empty($_SESSION))
{
	ini_set("session.cookie_httponly", 1);
	session_start();
}

if( !(isset($_SESSION['token']) and $_SESSION['token']==$_POST['token']) ){
	echo json_encode(array(
		"success"=>false,
		"message"=>"Request forgery detected"));
	exit;
}

include 'function.php';
header("Content-Type: application/json");

if(!isset($_SESSION['username'])){
	exit;
}

$username = $_SESSION['username'];
$self_id = getUserId($username);
$owner_id = getUserId($_POST['username']);
$event_id = $_POST['event_id'];

if(!addSharedEvent($event_id, $owner_id, $self_id)) {
	echo json_encode(
		array(
			"success"=>false,
			"message"=>"Cann't add shared event"
		)
	);
}

exit;
?>