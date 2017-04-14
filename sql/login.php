<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>News Login</title>
	<link rel="stylesheet" type="text/css" href="news.css"/>
</head>

<body>
	<h1>News</h1>
<?php
	$username = $_POST['username'];
	$pword = $_POST['password'];
	
	if( !preg_match('/^[\w_\.\-]+$/', $username) ){
		echo "Invalid username";
		exit;
	}
	if( !preg_match('/^[\w_\.\-]+$/', $pword) ){
		echo "Invalid password";
		exit;
	}
	
	// check login
	require 'mysqli.php';
	$stmt = $mysqli->prepare("select username from users");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();
	
	// Check user and pwd
	$userfound = false;
	while($row = $result->fetch_assoc()){
		if($row["username"]==$username){
			$userfound = true;
			$stmt = $mysqli->prepare("SELECT COUNT(*), userid, userhpw FROM users WHERE username=?");
			// Bind the parameter
			$stmt->bind_param('s', $username);
			$stmt->execute();
			// Bind the results
			$stmt->bind_result($cnt, $user_id, $pwd_hash);
			$stmt->fetch();
			// Compare the submitted password to the actual password hash
			if( $cnt == 1 && crypt($pword, $pwd_hash)==$pwd_hash){
				// Login succeeded!
				printf("<p>Hello, %s.</p>",	htmlentities($username)	);
				session_start();
				$_SESSION['username'] = $username;
				$_SESSION['userid'] = $user_id;
				// CSRF token
				$_SESSION['token'] = substr(md5(rand()), 0, 10);
				session_write_close();
				echo '<a href="news_list.php">Go to News</a><br />';
			}else{
				// Login failed; redirect back to the login screen
				echo 'Username or password incorrect!<br>';
				echo '<a href="login.html">Return to login</a>';				
			}
		}
	}	
	
	if(!$userfound) {
		echo 'User not found!<br>';
		echo '<a href="login.html">Return to login</a>';
	}
?>
</body>
</html>