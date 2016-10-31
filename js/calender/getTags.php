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


$my_array = array();
$tags = getTags();
foreach($tags as $tag){
	array_push($my_array, array(
		"id"=>$tag[0],
		"tag"=>$tag[1]
		));
}

echo json_encode($my_array);

exit;
?>