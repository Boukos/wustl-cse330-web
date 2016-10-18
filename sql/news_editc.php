<!DOCTYPE HTML>
<head><title> </title></head>

<body>
<?php
	$comment_id= $_POST["comment_id"];
	$comment = $_POST["comment"];

	printf("<FORM METHOD=\"POST\" ACTION=\"news_editc_process.php\">
	<input type=\"hidden\" name=\"comment_id\" value= %s>
	comment: <input type=\"text\" name=\"comment_new\"/ value=%s>
	<input type=\"submit\" value=\"submit\"/>	
	</FORM>",$comment_id,$comment);
	echo "<br>";
	echo "<a href=\"news_express.php\">Return to news</a>";
?>
</body>

</html>