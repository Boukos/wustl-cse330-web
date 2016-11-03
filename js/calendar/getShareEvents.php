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
$user_id = getUserId($username);
$events = getEvents($user_id);

$my_array = array();
$lastDate = null;
$tempArray = null;
foreach($events as $event){
	$dateString = strtotime($event[2]);
	$date = date("Y-m-d",$dateString);
	$time = date("H:i",$dateString);
	if($date != $lastDate){
		if($tempArray != null){
			$my_array[$lastDate] = $tempArray;
		}
		$lastDate = $date;
		$tempArray = array();
	}
	array_push($tempArray, array(
		"id"=>$event[0],
		"title"=>$event[1],
		"time"=>$time,
		"tag_id"=>$event[3]
		));
}
$my_array[$lastDate] = $tempArray;

echo json_encode($my_array);
exit;
?>