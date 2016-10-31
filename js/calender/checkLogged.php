<?php
if(empty($_SESSION))
{
	ini_set("session.cookie_httponly", 1);
	session_start();
}

header("Content-Type: application/json");
if(isset($_SESSION['username'])){
	echo json_encode(array("keep"=>true));
}
else{
	echo json_encode(array("keep"=>false));
}
?>