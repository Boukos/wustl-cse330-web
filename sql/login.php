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
	
	// read user list
	require 'database.php';

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
			// Use a prepared statement
			$stmt = $mysqli->prepare("SELECT userid, userhpw, usersalt FROM users WHERE username=?");
			// Bind the parameter
			$stmt->bind_param('s', $username);
			$stmt->execute();
			// Bind the results
			$stmt->bind_result($user_id, $pwd_hash, $user_salt);
			$stmt->fetch();
			// Compare the submitted password to the actual password hash
			if( crypt($pword, $user_salt)==$pwd_hash){
				// Login succeeded!
				printf("<p>Hello, %s; how do you do?</p>",
					htmlentities($username)
				);
				session_start();
				$_SESSION['username'] = $username;
				$_SESSION['userid'] = $user_id;
				$_SESSION['token'] = substr(md5(rand()), 0, 10);
				session_write_close();
				echo '<a href="news_express.php">Continue to news</a>';
				// Redirect to your target page
			}else{
				// Login failed; redirect back to the login screen
				echo 'Wrong password!<br>';
				echo '<a href="login.html">Return to login</a>';				
			}
		}
	}	
	
	if(!$userexists) {
		echo 'No such user!<br>';
		echo '<a href="login.html">Return to login</a>';
	}
?>