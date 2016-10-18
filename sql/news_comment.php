<!DOCTYPE HTML>
<head><title> </title></head>

<body>
<?php
	$story_id= $_POST["story_id"];
	$author_id=$_POST["author_id"];

	printf("Comment on %s as user %s"."\n", $story_id, $author_id);

	printf("<FORM METHOD=\"POST\" ACTION=\"news_comment_process.php\">
	Comment: <input type=\"text\" name=\"comment\"/>
	<input type=\"submit\" value=\"submit\"/>
	<input type=\"hidden\" name=\"story_id\" value= %s>	
	<input type=\"hidden\" name=\"author_id\" value= %s> 
	</FORM>",$story_id,$author_id);
	echo "<br>";
	echo "<a href=\"news_express.php\">Return to news</a>";
?>
</body>

</html>