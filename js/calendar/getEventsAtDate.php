<?php
error_reporting(0);

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
$user_id = getUserId($username);
$events = getEvents($user_id);

$thedate = $_POST['thedate'];

$my_array = array();
$tempArray = null;
foreach($events as $event){
	$dateString = strtotime($event[2]);
	$date = date("Y-m-d",$dateString);
	$time = date("H:i",$dateString);
	if($date==$thedate){
		array_push($tempArray, array(
			"id"=>$event[0],
			"title"=>htmlentities($event[1]),
			"time"=>$time,
			"tag_id"=>$event[3]
			));
	}
}

echo json_encode($tempArray);
exit;
?>