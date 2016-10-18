<!DOCTYPE html>
<html>
<head>
	<title>CSE330 M3 News</title>
	<style type="text/css">
	form {
		display: inline-block;
	}
	</style>
</head>

<body>
<?php
	session_start();
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf("Welcome, %s <a href=\"logout.php\">Log out</a> ",$_SESSION['username']);
		printf('<a href="news_profile.php">My Profile</a>');
	}
	else{
		printf('Welcome, anonymous user, <a href="login.html">Log In</a>');
	}
	echo('<br>');
	
	require 'database.php';

	$stmt = $mysqli->prepare("select id, title, story, link, author_id, time from stories order by time DESC");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	 
	$stmt->execute();
	$result = $stmt->get_result();

	echo "<ul>\n";
	while($row = $result->fetch_assoc()){
		printf(
			"\t<li>%s <br> %s <a href= \"%s\">Link</a> <br> %s %s</li>\n",
			htmlspecialchars( $row["title"] ),
			htmlspecialchars( $row["story"] ),
			htmlspecialchars( $row["link"] ),
			htmlspecialchars( $row["author_id"] ),
			htmlspecialchars( $row["time"] )
		);
		printf("<a class=\"twitter-share-button\"
		href=\"https://twitter.com/intent/tweet?text=%s \">
		Tweet</a>", $row["title"]);

		printf("
			<FORM METHOD=\"POST\" ACTION=\"news_view.php\">
			<input type=\"hidden\" name=\"story_id\" value= %s>	
			<input type=\"hidden\" name=\"title\" value= %s>	
			<INPUT TYPE=\"submit\" VALUE=\"view\">
			</FORM>",$row["id"],$row["title"]
		);
		
		if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
			printf("
				<FORM METHOD=\"POST\" ACTION=\"news_comment.php\">
				<input type=\"hidden\" name=\"story_id\" value= %s>	
				<input type=\"hidden\" name=\"author_id\" value= %s> 
				<INPUT TYPE=\"submit\" VALUE=\"comment\">
				</FORM>",$row["id"],$row["author_id"]
			);
		}
		
		if((isset($_SESSION['username']) && !empty($_SESSION['username'])) &&
		(($row["author_id"]==$_SESSION['userid'])||($_SESSION['username']=='admin') )){
			printf(" 
				<FORM METHOD=\"POST\" ACTION=\"news_delete.php\">
				<input type=\"hidden\" name=\"story_id\" value= %s>
				<INPUT TYPE=\"submit\" VALUE=\"delete\">
				</FORM>",$row["id"]
				);
			printf(" 
				<FORM METHOD=\"POST\" ACTION=\"news_edit.php\">
				<input type=\"hidden\" name=\"story_id\" value= %s>
				<input type=\"hidden\" name=\"title\" value= %s> 
				<input type=\"hidden\" name=\"story\" value= %s>  
				<INPUT TYPE=\"submit\" VALUE=\"edit\">
				</FORM>",$row["id"],$row["title"],$row["story"]
			);
		}
	}
	
	if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
		echo('<br><br>');
		printf("<FORM METHOD=\"POST\" ACTION=\"news_post.php\">
		<input type=\"hidden\" name=\"token\" value=%s />
		<INPUT TYPE=\"submit\" VALUE=\"Post a story\">
		</FORM>",$_SESSION['token']);
	}
	$stmt->close();
?>

</body>
</html>