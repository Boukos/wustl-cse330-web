<?php
	require 'database.php';
 
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
echo "<a href=\"news_express.php\">Return to news</a>";
?>