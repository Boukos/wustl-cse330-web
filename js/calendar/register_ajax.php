<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
require 'mysqli.php';
$username = $mysqli->real_escape_string($_POST['username']);
$password = $mysqli->real_escape_string($_POST['password']);

if( !preg_match('/^[\w_\.\-]+$/', $username) ){
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid username"
	));
	exit;
}
if( !preg_match('/^[\w_\.\-]+$/', $password) ){
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid Password"
	));
	exit;
}

// check if user existed
$stmt = $mysqli->prepare("select username from users");
if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$userfound = false;
while($row = $result->fetch_assoc()){
	if($row["username"]==$username){
		$userfound = true;
		echo json_encode(array(
			"success" => false,
			"message" => "Username not available"
		));
		exit;
	}
}

// new user, insert user data into database
if(!$userfound){
	$stmt = $mysqli->prepare("insert into users (username,password) values (?, ?)");
	if(!$stmt){
		echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
		));
		exit;
	}
	$userpassword = crypt($password);
	$stmt->bind_param('ss', $username, $userpassword);
	$stmt->execute();
	$stmt->close();
	echo json_encode(array(
		"success" => true,
		"message" => "Successfully registered"
	));
	
}

?>