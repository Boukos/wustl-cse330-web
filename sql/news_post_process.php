<?php
	require 'database.php';
	$title = $mysqli->real_escape_string($_POST['title']);
	$story = $mysqli->real_escape_string($_POST['story']);
	$link = $mysqli->real_escape_string($_POST['link']);
	session_start();
	$name= $_SESSION['userid'];
	
	$stmt = $mysqli->prepare("insert into stories (title, story, link, author_id) values (?, ?, ?, ?)");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	if($_SESSION['token'] !== $_POST['token']){
		die("Request forgery detected");
	}
	$stmt->bind_param('ssss', $title, $story, $link, $name);
	$stmt->execute(); 
	$stmt->close();

	header("Location: news_express.php");
	exit; 
	 
?>
