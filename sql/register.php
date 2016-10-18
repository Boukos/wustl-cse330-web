<?php
require 'database.php';
$username = $mysqli->real_escape_string($_POST['username']);
$password = $mysqli->real_escape_string($_POST['password']);
$passwordagain = $mysqli->real_escape_string($_POST['passwordagain']);

if( !preg_match('/^[\w_\.\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}
if( !preg_match('/^[\w_\.\-]+$/', $password) ){
	echo "Invalid password";
	exit;
}
if( !preg_match('/^[\w_\-]+$/', $passwordagain) ){
	echo "Invalid password check";
	exit;
}
if( $password != $passwordagain){
	echo "Password do not match";
	exit;
}

$stmt = $mysqli->prepare("select username from users");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$userexists = false;
while($row = $result->fetch_assoc()){
	if($row["username"]==$username){
		$userexists = true;
		echo "Username exists";
		exit;
	}
}

if(!$userexists){
	$stmt = $mysqli->prepare("insert into users (username,userhpw,usersalt,usersignuptime) values (?, ?, ?,NOW())");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$salt = 'salt';
	$userhpw = crypt($password,$salt);
	$stmt->bind_param('sss', $username, $userhpw, $salt);
	$stmt->execute();
	$stmt->close();
	echo "Success!";
}
echo "<br>";
echo "<a href=\"login.html\">Login</a>";
echo "<br>";
echo "<a href=\"news_express.php\">Return to news</a>";

?>