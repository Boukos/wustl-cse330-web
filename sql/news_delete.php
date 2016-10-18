<?php
	require 'database.php';
 
	$id= $_POST['story_id'];
	
	$stmt = $mysqli->prepare("DELETE FROM stories WHERE stories.id=?");
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