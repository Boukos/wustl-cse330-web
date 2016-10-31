<?php
if(empty($_SESSION))
{
	session_start();
}

// if( !(isset($_SESSION['token']) and $_SESSION['token']==$_POST['token']) ){
// 	echo json_encode(array(
// 		"success"=>false,
// 		"message"=>"Request forgery detected"));
// 	exit;
// }

include 'function.php';
header("Content-Type: application/json");

// if(!isset($_SESSION['username'])){
// 	exit;
// }


if(isset($_POST['event_id'])){
	$eid = $_POST['event_id'];
	deleteEvent($eid);
}

echo json_encode(array(
	"success"=>true));
exit;
?>