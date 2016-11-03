<?php
if (empty($_SESSION)){
	ini_set("session.cookie_httponly", 1);
	session_start();
}

include 'function.php';

header("Content-Type: application/json");

if (isset($_POST['username']) and isset($_POST['password'])){
	$username = $_POST['username'];
	$password = $_POST['password'];
	$result = login($username,$password);
	if($result == "success"){
		$_SESSION['username'] = $username;
		$_SESSION['token'] = substr(md5(rand()), 0, 10);
		echo json_encode(array(
			"success"=>true,
			"token"=>$_SESSION['token']));
	}
	else{
		echo json_encode(array(
		"success"=>false,
		"message"=>$result));
	}
	exit;
}
else{
	echo json_encode(array(
		"success"=>false,
		"message"=>"please input username or password"));
	exit;
}
?>