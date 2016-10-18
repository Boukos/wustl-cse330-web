<!DOCTYPE HTML>
<head><title> </title></head>

<body>
<?php
	$story_id= $_POST["story_id"];
	$title = $_POST["title"];
	$story=$_POST["story"];

	printf("<FORM METHOD=\"POST\" ACTION=\"news_edit_process.php\">
	<input type=\"hidden\" name=\"story_id\" value= %s>
	title: <input type=\"text\" name=\"title_new\"/ value=%s>
	story: <input type=\"text\" name=\"story_new\"/ value=%s>
	<input type=\"submit\" value=\"submit\"/>	
	</FORM>",$story_id,$story,$title);
	echo "<br>";
	echo "<a href=\"news_express.php\">Return to news</a>";
?>
</body>

</html>