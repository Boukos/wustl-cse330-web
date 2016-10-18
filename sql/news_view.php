<!DOCTYPE html>
<html>
<head>
	<title>CSE330 M3 View</title>
	<style type="text/css">
	form {
		display: inline-block;
	}
	</style>
</head>

<body>
<?php
	$story_id = $_POST['story_id'];
	$story_title = $_POST['title'];
	session_start();
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf("Welcome, %s <a href=\"logout.php\">Log out</a>",$_SESSION['username']);
	}
	else{
		printf('Welcome, anonymous user, <a href="login.html">Log In</a>');
	}
	echo('<br><br>');
	echo($story_title);
	echo('<br>');
	
	require 'database.php';
	$stmt = $mysqli->prepare("select comment_id, comment, user_id, comment_time from comments  where story_id=? order by comment_time DESC");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('s', $story_id);
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<ul>\n";
	while($row = $result->fetch_assoc()){
		printf(
			"\t<li>%s %s <br> %s %s</li>\n",
			htmlspecialchars( $row["comment_id"] ),
			htmlspecialchars( $row["comment"] ),
			htmlspecialchars( $row["user_id"] ),
			htmlspecialchars( $row["comment_time"] )
		);
		
		if((isset($_SESSION['username']) && !empty($_SESSION['username'])) &&
		($row["user_id"]==$_SESSION['userid'])){
			printf(" 
				<FORM METHOD=\"POST\" ACTION=\"news_deletec.php\">
				<input type=\"hidden\" name=\"comment_id\" value= %s>
				<INPUT TYPE=\"submit\" VALUE=\"delete\">
				</FORM>",$row["comment_id"]
				);
			printf(" 
				<FORM METHOD=\"POST\" ACTION=\"news_editc.php\">
				<input type=\"hidden\" name=\"comment_id\" value= %s>
				<input type=\"hidden\" name=\"comment\" value= %s> 
				<INPUT TYPE=\"submit\" VALUE=\"edit\">
				</FORM>",$row["comment_id"],$row["comment"]
			);
		}
	}
	echo('<br><br>');
	if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf("
		<FORM METHOD=\"POST\" ACTION=\"news_comment.php\">
		<input type=\"hidden\" name=\"story_id\" value= %s>	
		<input type=\"hidden\" name=\"author_id\" value= %s> 
		<INPUT TYPE=\"submit\" VALUE=\"comment\">
		</FORM>",$story_id,$_SESSION['userid']
		);
	}
	$stmt->close();
	
	echo '<a href="news_express.php">Return to news</a>'
?>

</body>
</html>