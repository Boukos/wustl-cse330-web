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
	$story_id= $_POST["story_id"];
	$author_id=$_POST["author_id"];

	printf("Comment on %s as user %s <br />", $story_id, $author_id);

	printf(
		"<form method=\"POST\" action=\"news_handler.php\">
		<label>Comment: <input type=\"text\" name=\"comment\"/></label>
		<input type=\"submit\" value=\"submit\"/>
		<input type=\"hidden\" name=\"story_id\" value= %s>	
		<input type=\"hidden\" name=\"author_id\" value= %s> 
		<input type=\"hidden\" name=\"operation\" value= \"comment\"> 
		<input type=\"hidden\" name=\"token\" value=%s />
		</form>"
	,$story_id,$author_id,$_SESSION['token']);
	echo "<br />";
	echo "<a href=\"news_list.php\">Return to news</a>";
?>
</body>

</html>