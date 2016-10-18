<!DOCTYPE html>
<html>
<head>
	<title>CSE330 M3 News</title>
	<style type="text/css">
	form {
		display: inline-block;
	}
	</style>
</head>

<body>
<?php
	session_start();
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf("Hello, %s <a href=\"logout.php\">Log out</a>",$_SESSION['username']);
	}
	else{
		printf( 'Hello, anonymous user, <a href="login.html">Log In</a>');
	}
	echo '<br>';
?>

blah blah blah<br>
</body>
</html>