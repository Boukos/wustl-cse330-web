<!DOCTYPE html>
<html>
<head>
	<title>News Comment Delete</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Delete</h1>
	
<?php
	session_start();
	echo "<strong>You are going to delete this comment.</strong>";
	$comment_id= $_POST["comment_id"];
	printf(
		"<form method=\"POST\" action=\"news_comment_handler.php\">
		<input type=\"submit\" value=\"continue\"/>
		<input type=\"hidden\" name=\"comment_id\" value= %s>	
		<input type=\"hidden\" name=\"operation\" value= \"delete\"> 
		<input type=\"hidden\" name=\"token\" value=%s />
		</form><br /><br />"
	,$comment_id,$_SESSION['token']);
	echo "<a href=\"news_list.php\">Return to news</a>";
?>
	
</body>

</html>