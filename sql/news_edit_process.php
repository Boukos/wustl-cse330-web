<?php
	require 'database.php';
	
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
	echo "<a href=\"news_express.php\">Return to news</a>";
?>
