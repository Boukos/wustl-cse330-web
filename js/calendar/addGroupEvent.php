<?php
if(empty($_SESSION))
{
	ini_set("session.cookie_httponly", 1);
	session_start();
}

if( !(isset($_SESSION['token']) and $_SESSION['token']==$_POST['token']) ){
	echo json_encode(array(
		"success"=>false,
		"tokens"=>$_SESSION['token'],
		"tokenp"=>$_POST['token'], 
		"message"=>"Request forgery detected"));
	exit;
}

include 'function.php';
header("Content-Type: application/json");

if(!isset($_SESSION['username'])){
	echo json_encode(array(
		"success"=>false,
		"message"=>"No username.."));
	exit;
}

$content = $_POST['content'];
$timestamp = $_POST['timestamp'];
$username = $_SESSION['username'];
$tag_id = $_POST['tag_id'];

$user_id = getUserId($username);

//$addEventResult = addEvent($user_id,$content,$timestamp,$tag_id);
if(addEvent($user_id,$content,$timestamp,$tag_id)!='Success') {
	echo json_encode(
		array(
			"success"=>false,
			"message"=>"failed"
		)
	);
}
else{
	echo json_encode(
		array(
			"success"=>true,
			"message"=>"succeeded"
		)
	);
}

exit;
?>