<!DOCTYPE html>
<html>
<head>
	<title>News Content Edit</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Content Edit</h1>
<?php
	session_start();
	
	$story_id= $_POST["story_id"];
	$title = $_POST["title"];
	$story=$_POST["story"];

	printf("<form method=\"POST\" action=\"news_handler.php\">
	<input type=\"hidden\" name=\"story_id\" value= %s>
	<input type=\"hidden\" name=\"operation\" value= \"edit\">
	title: <input type=\"text\" name=\"title_new\" value=\"%s\">
	story: <input type=\"text\" name=\"story_new\" value=\"%s\">
	<input type=\"submit\" value=\"submit\"/>
	<input type=\"hidden\" name=\"token\" value=%s />
	</form>",$story_id,$story,$title,$_SESSION['token']);
	echo "<br /><br />";
	echo "<a href=\"news_list.php\">Return to news</a>";
?>
</body>

</html>