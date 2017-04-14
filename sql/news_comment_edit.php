<!DOCTYPE html>
<html>
<head>
	<title>News Comment Edit</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Edit</h1>
	
<?php
	session_start();
	$comment_id= $_POST["comment_id"];
	$comment = $_POST["comment"];

	printf(
		"<form method=\"POST\" action=\"news_comment_handler.php\">
		<input type=\"hidden\" name=\"comment_id\" value= \"%s\">
		<input type=\"hidden\" name=\"operation\" value= \"edit\">
		Comment: <input type=\"text\" name=\"comment_new\" value=\"%s\">
		<input type=\"submit\" value=\"submit\"/>
		<input type=\"hidden\" name=\"token\" value=%s />
		</form>"
		,$comment_id,$comment,$_SESSION['token']);
	echo "<br /><br />";
	echo "<a href=\"news_list.php\">Return to news</a>";
?>
</body>

</html>