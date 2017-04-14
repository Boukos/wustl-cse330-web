<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>News Register</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Register</h1>
	<div id="main">

<?php
require 'mysqli.php';
$username = $mysqli->real_escape_string($_POST['username']);
$password = $mysqli->real_escape_string($_POST['password']);
$passwordre = $mysqli->real_escape_string($_POST['passwordre']);

if( !preg_match('/^[\w_\.\-]+$/', $username) ){
	echo "Invalid username";
	echo "</div></body></html>";
	exit;
}
if( !preg_match('/^[\w_\.\-]+$/', $password) ){
	echo "Invalid password";
	echo "</div></body></html>";
	exit;
}
if( !preg_match('/^[\w_\-]+$/', $passwordre) ){
	echo "Invalid password re-enterred";
	echo "</div></body></html>";
	exit;
}
if( $password != $passwordre){
	echo "Passwords do not match";
	echo "</div></body></html>";
	exit;
}

// check if user existed
$stmt = $mysqli->prepare("select username from users");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->execute();
$result = $stmt->get_result();
$userfound = false;
while($row = $result->fetch_assoc()){
	if($row["username"]==$username){
		$userfound = true;
		echo "Username exists";
		echo "</div></body></html>";
		exit;
	}
}

// new user, insert user data into database
if(!$userfound){
	$stmt = $mysqli->prepare("insert into users (username,userhpw,usersignuptime) values (?, ?,NOW())");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$userhpw = crypt($password);
	$stmt->bind_param('ss', $username, $userhpw);
	$stmt->execute();
	$stmt->close();
	echo "Success!";
}
echo "<br />";
echo "<a href=\"login.html\">Login</a>";
echo "<br />";
echo "<a href=\"news_list.php\">Return to news</a>";

?>
	</div>
</body>
</html>