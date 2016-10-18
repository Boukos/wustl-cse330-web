<!DOCTYPE HTML>
<head><title> </title></head>

<body>
<?php
	session_start();
	printf("Post as %s",$_SESSION['username']);
?>
<br>
<form name="submit" action="news_post_process.php" method= "post">
	Title: <input type="text" name="title"/>
	Story: <input type="text" name="story"/>
	Link: <input type="text" name="link"/>
<?php
	printf("<input type=\"hidden\" name=\"token\" value=%s />",$_POST['token'])
?>
	<input type="submit" value="submit"/>
</form>
<br>
<a href="news_express.php">Go back</a>

</body>
</html>