<!DOCTYPE html>
<html>
<head>
	<title>News Comment</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Comment</h1>
<?php
	session_start();
	if($_SESSION['token'] !== $_POST['token']){
		die("Request forgery detected");
	}

	require 'mysqli.php';
	
	if($_POST["operation"]=="edit"){
		$comment_id = $_POST['comment_id'];
		$comment = $_POST['comment_new'];
		
		$stmt = $mysqli->prepare("UPDATE comments SET comment=? WHERE comment_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('ss',$comment,$comment_id);
		$stmt->execute();
		$stmt->close();
		echo "Success!<br>";
		echo "<a href=\"news_list.php\">Return to news</a>";
	}
	else if($_POST["operation"]=="delete"){
		$id= $_POST['comment_id'];
		
		$stmt = $mysqli->prepare("DELETE FROM comments WHERE comments.comment_id=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		  }

		$stmt->bind_param('s', $id);
		$stmt->execute();
		$stmt->close();

		echo "Success!<br>";
		echo "<a href=\"news_list.php\">Return to news</a>";
	}
?>
</body>
</html>