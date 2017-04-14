<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
$username = $_POST['username'];
$password = $_POST['password'];

// Check to see if the username and password are valid.
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

// check login
require 'mysqli.php';
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

// Check user and pwd
$userfound = false;
$userlogin = false;
while($row = $result->fetch_assoc()){
	if($row["username"]==$username){
		$userfound = true;
		$stmt = $mysqli->prepare("SELECT COUNT(*), userid, password FROM users WHERE username=?");
		// Bind the parameter
		$stmt->bind_param('s', $username);
		$stmt->execute();
		// Bind the results
		$stmt->bind_result($cnt, $userid, $password_db);
		$stmt->fetch();
		// Compare the submitted password to the actual password hash
		if( $cnt == 1 && crypt($password, $password_db)==$password_db){
			// Login succeeded!
			$userlogin = true;
		}else{
			// Login failed; redirect back to the login screen
			$userlogin = false;
		}
		break;
	}
}	

// login result
if( $userlogin ){
	session_start();
	$_SESSION['username'] = $username;
	$_SESSION['userid'] = $userid;
	$_SESSION['token'] = substr(md5(rand()), 0, 10);
 
	echo json_encode(array(
		"success" => true,
		"token" => $_SESSION['token']
	));
	exit;
}else{
	if(!$userfound) {
		echo json_encode(array(
			"success" => false,
			"message" => "Username not exists"
		));
		exit;
	}
	else{
		echo json_encode(array(
			"success" => false,
			"message" => "Incorrect Username or Password"
		));
		exit;	
	}	
}
?>