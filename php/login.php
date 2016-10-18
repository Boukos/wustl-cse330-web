<?php
	$name = $_GET['username'];
	
	// read user list
	$userlist = array();
	$h = fopen("/home/hyfan/user_files/userlist.txt", "r");
	$linenum = 1;
	while( !feof($h) ){
		array_push($userlist,trim(fgets($h)));
	}
	fclose($h);

	if(in_array($name,$userlist)) {
		printf("<p>Hello, %s; how do you do?</p>",
			htmlentities($name)
		);
		session_start();
		$_SESSION['username'] = $name;
		session_write_close();
		echo '<a href="filelist.php">Continue to my file</a>';
	}
	else {
		echo 'No such user!<br>';
		echo '<a href="login.html">Return to login</a>';
	}
?>