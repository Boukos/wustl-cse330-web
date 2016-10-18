<?php
	require 'database.php';
	
	session_start();
	
	$stmt = $mysqli->prepare("SELECT userid, userintro,usersignuptime FROM users WHERE username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('s',$_SESSION['username']);
	$stmt->execute();
	$stmt->bind_result($id, $intro, $time);
	$stmt->fetch();
	
	printf("User %s <br>",$_SESSION['username']);
	printf("Id %s <br>",$id);
	printf("Desc %s <br>",$intro);
	printf("Member Since %s <br>",$time);
	
	echo "<br>";
	echo "<a href=\"news_express.php\">Return to news</a>";
?>