<!DOCTYPE html>
<html>
<head>
	<title>News Delete</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Delete</h1>
<?php
	session_start();
	echo "<strong>You are going to delete this news.</strong>";
	$story_id= $_POST["story_id"];
	printf(
		"<form method=\"POST\" action=\"news_handler.php\">
		<input type=\"submit\" value=\"continue\"/>
		<input type=\"hidden\" name=\"story_id\" value= %s>	
		<input type=\"hidden\" name=\"operation\" value= \"delete\"> 
		<input type=\"hidden\" name=\"token\" value=%s />
		</form><br /><br />"
		,$story_id,$_SESSION['token']);
	echo "<a href=\"news_list.php\">Return to news</a>";
?>
	
</body>

</html>