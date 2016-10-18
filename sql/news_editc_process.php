<?php
	require 'database.php';
	
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
	echo "<a href=\"news_express.php\">Return to news</a>";
?>
