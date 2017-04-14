<!DOCTYPE html>
<html>
<head>
	<title>News</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>News</h1>
	
	<?php
	session_start();
	
	require 'mysqli.php';
	
	if($_SESSION['token'] !== $_POST['token']){
		die("Request forgery detected");
	}
	
	if ($_POST["operation"] == "comment"){
		$story =$mysqli->real_escape_string($_POST['story_id']);
		$author=$mysqli->real_escape_string($_POST['author_id']);
		$user = $_SESSION['userid'];
		$comment=$mysqli->real_escape_string($_POST['comment']);

		$stmt = $mysqli->prepare("insert into comments (story_id, user_id, comment,comment_time) values (?, ?, ?, NOW())");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->bind_param('sss', $story, $user, $comment);
		$stmt->execute();
		$stmt->close();

		echo "Success!<br />";
		echo "<a href=\"news_list.php\">Return to news</a>";
	}
	else if ($_POST["operation"] == "delete"){
		$id= $_POST['story_id'];
	
		$stmt = $mysqli->prepare("DELETE FROM stories WHERE stories.id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$stmt->close();

		echo "Success!<br />";
		echo "<a href=\"news_list.php\">Return to news</a>";
	}
	else if ($_POST["operation"] == "edit"){
		$story_id = $_POST['story_id'];
		$title = $_POST['title_new'];
		$story = $_POST['story_new'];
		
		$stmt = $mysqli->prepare("UPDATE stories SET title=?,story=? WHERE id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('sss',$title,$story,$story_id);
		$stmt->execute();
		$stmt->close();
		echo "Success!<br>";
		echo "<a href=\"news_list.php\">Return to news</a>";
	}
?>
</body>
</html>