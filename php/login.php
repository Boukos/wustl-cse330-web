<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>File Sharing Site Login</title>
	<link rel="stylesheet" type="text/css" href="FileSharingSite.css" />
</head>

<body>
<p>
<?php
	// get the username input
	$username = $_GET["username"];
	
	// retrieve the current user list
	// from CSE330 wiki
	$userlist = array();
	$file = fopen("/home/hyfan/userfiles/userlist.txt", "r");
	$linenum = 1;
	while( !feof($file) ){
		$linenum++;
		array_push($userlist,trim(fgets($file)));
	}
	fclose($file);
	
	// check if user exists
	if(in_array($username,$userlist)) {
		// record the current user
		session_start();
		$_SESSION['username'] = $username;
		session_write_close();
		
		echo 'Login successfully;';
		echo '<a href="filesharing.php">File list</a>';
	}
	else {
		echo 'User not exists!<br>';
		echo '<a href="login.html">Return.</a>';
	}
?>

</p>
</body>

</html>