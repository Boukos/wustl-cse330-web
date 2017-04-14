<!DOCTYPE HTML>
<head>
	<title>News</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>User Information</h1>
	<div id="main">
<?php
	require 'mysqli.php';
	
	session_start();
	// get user information
	$stmt = $mysqli->prepare("SELECT userid, userintro, usersignuptime FROM users WHERE username=?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmt->bind_param('s',$_SESSION['username']);
	$stmt->execute();
	$stmt->bind_result($id, $intro, $time);
	$stmt->fetch();
	$stmt->close();
	
	// get user activities
	$stmts = $mysqli->prepare("SELECT COUNT(*) FROM stories WHERE author_id=?");
	if(!$stmts){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmts->bind_param('s',$id);
	$stmts->execute();
	$stmts->bind_result($n_story);
	$stmts->fetch();
	$stmts->close();
	
	$stmtc = $mysqli->prepare("SELECT COUNT(*) FROM comments WHERE user_id=?");
	if(!$stmtc){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$stmtc->bind_param('s',$id);
	$stmtc->execute();
	$stmtc->bind_result($n_comment);
	$stmtc->fetch();
	$stmtc->close();
	
	// display 
	printf("User: %s <br />",$_SESSION['username']);
	printf("Id: %s <br />",$id);
	printf("Desc: %s <br />",$intro);
	printf("Member Since: %s <br />",$time);
	printf("Stories Posted: %s <br />",$n_story);
	printf("Comments Posted: %s <br />",$n_comment);
	
	echo "<br />";
	echo "<a href=\"news_list.php\">Return to News</a>";
?>
	</div>
</body>
</html>