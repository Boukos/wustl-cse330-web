<?php
	require 'database.php';
	
	$story =$mysqli->real_escape_string($_POST['story_id']);
	$author=$mysqli->real_escape_string($_POST['author_id']);
	session_start();
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

	echo "Success!<br>";
	echo "<a href=\"news_express.php\">Return to news</a>";
 
?>