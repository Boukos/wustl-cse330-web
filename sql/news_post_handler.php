<!DOCTYPE html>
<html>
<head>
	<title>News</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Post</h1>
<?php
	require 'mysqli.php';
	$title = $mysqli->real_escape_string($_POST['title']);
	$story = $mysqli->real_escape_string($_POST['story']);
	$link = $mysqli->real_escape_string($_POST['link']);
	session_start();
	$user_id= $_SESSION['userid'];
	
	// check if title is empty
	if ($title==""){
		echo "Title cannot be empty<br />";
		echo "<a href=\"news_post.php\">Return</a>";
		exit;
	}
	
	// insert new story to database
	$stmt = $mysqli->prepare("insert into stories (title, story, link, author_id) values (?, ?, ?, ?)");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	// CSRF
	if($_SESSION['token'] !== $_POST['token']){
		die("Request forgery detected");
	}
	$stmt->bind_param('ssss', $title, $story, $link, $user_id);
	$stmt->execute(); 
	$stmt->close();

	header("Location: news_list.php");
	exit; 
	 
?>
</body>
</html>