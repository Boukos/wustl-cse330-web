<!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>Pet Login</title>
<style type="text/css">
body{
	width: 760px; /* how wide to make your web page */
	background-color: teal; /* what color to make the background */
	margin: 0 auto;
	padding: 0;
	font:12px/16px Verdana, sans-serif; /* default font */
}
div#main{
	background-color: #FFF;
	margin: 0;
	padding: 10px;
}
</style>
</head>
<body><div id="main">
 
<!-- CONTENT HERE -->
<?php
	if(empty($_POST)){
echo '<form action="pet-login.php" method="post">
	<input type="text" name="username">
	<input type="password" name="password">
	<input type="submit" value="login">
	<input type="submit" value="register">
</form>';
	}
	else{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if( !preg_match('/^[\w_\.\-]+$/', $username) ){
			echo "Invalid username";
			exit;
		}
		if( !preg_match('/^[\w_\.\-]+$/', $password) ){
			echo "Invalid password";
			exit;
		}
	
		if (isset($_POST['register'])) {
			require 'database.php';
			$username = $mysqli->real_escape_string($_POST['username']);
			$password = $mysqli->real_escape_string($_POST['password']);
			
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
				$stmt = $mysqli->prepare("insert into users (username,password) values (?, ?)");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$userhpw = crypt($password);
				$stmt->bind_param('ss', $username, $password);
				$stmt->execute();
				$stmt->close();
				echo "Success!";
			}
		} else { // login
			require 'database.php';
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
					$stmt = $mysqli->prepare("SELECT COUNT(*), username, password FROM users WHERE username=?");
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
						echo '<a href="pet-listings.php">Go to pets</a><br />';
					}else{
						// Login failed; redirect back to the login screen
						echo 'Username or password incorrect!<br>';
						echo '<a href="pet-listings.php">Return to login</a>';				
					}
				}
			}	
			
			if(!$userfound) {
				echo 'User not found!<br>';
				echo '<a href="pets-listings.php">Return to login</a>';
			}
		}
	}
?>
 
</div></body>
</html>