<!DOCTYPE html>
<html>
<head>
	<title>News Content</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>News</h1>
<?php
	$story_id = $_POST['story_id'];
	$story_title = $_POST['title'];
	session_start();

	printf("<h2>%s </h2>",$story_title);
	
	require 'mysqli.php';
	$stmt = $mysqli->prepare("select comment_id, comment, user_id, comment_time from comments  where story_id=? order by comment_time DESC");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $story_id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	echo "<h3>Comments:</h3>";
	echo "<ul>\n";
	while($row = $result->fetch_assoc()){
		echo "<li>";
		printf(
			"%s %s <br /> %s %s <br />",
			htmlspecialchars( $row["comment_id"] ),
			htmlspecialchars( $row["comment"] ),
			htmlspecialchars( $row["user_id"] ),
			htmlspecialchars( $row["comment_time"] )
		);
		
		if((isset($_SESSION['username']) && !empty($_SESSION['username'])) &&
		($row["user_id"]==$_SESSION['userid'])){
			printf( 
				"<form method=\"POST\" action=\"news_comment_delete.php\">
				<input type=\"hidden\" name=\"comment_id\" value= \"%s\">
				<input type=\"submit\" value=\"delete\">
				</form>",$row["comment_id"]
				);
			printf( 
				"<form method=\"POST\" action=\"news_comment_edit.php\">
				<input type=\"hidden\" name=\"comment_id\" value= \"%s\">
				<input type=\"hidden\" name=\"comment\" value= \"%s\"> 
				<input type=\"submit\" value=\"edit\">
				</form>",$row["comment_id"],$row["comment"]
			);
		}
		echo "</li>";
	}
	echo "</ul>";
	
	if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf(
			"<form method=\"post\" action=\"news_comment.php\">
			<input type=\"hidden\" name=\"story_id\" value= %s>	
			<input type=\"hidden\" name=\"author_id\" value= %s> 
			<input type=\"submit\" value=\"comment\">
			</form>",$story_id,$_SESSION['userid']
		);
	}
	$stmt->close();
	
	echo "<br /><br />";
	echo '<a href="news_list.php">Return to news</a>';
?>

</body>
</html>