<!DOCtype html>
<html>
<head>
	<title>News</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>News</h1>
	<div id="main">
<?php
	// check login
	session_start();
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])){
		printf("Hello, %s <a href=\"logout.php\">Log out</a> ",$_SESSION['username']);
		echo '<a href="news_userinfo.php">Profile</a>';
	}
	else{
		echo 'Hello, anonymous user, <a href="login.html">Log In</a>' ;
	}
	echo('<br>');
	
	// database query
	// CSE330 Wiki
	require 'mysqli.php';
	$stmt = $mysqli->prepare("select id, title, story, link, author_id, time from stories order by time DESC");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();

	// List the news
	echo "<ul>\n";
	while($row = $result->fetch_assoc()){
		echo "<li>";
		printf(
			"<strong>%s</strong> ", htmlspecialchars( $row["title"] )
		);
		printf(
			"<a class=\"twitter-share-button\" href=\"https://twitter.com/intent/tweet?text=%s \">Tweet</a>"
			, str_replace(' ', '%20', $row["title"]) 
		);
		printf(
			"<br /> %s <a href= \"%s\">Link</a> <br /> %s %s\n",
			htmlspecialchars( $row["story"] ),
			htmlspecialchars( $row["link"] ),
			htmlspecialchars( $row["author_id"] ),
			htmlspecialchars( $row["time"] )
		);
		
		// form
		// view news
		printf(
			"<form method=\"POST\" action=\"news_content.php\">
			<input type=\"hidden\" name=\"story_id\" value= \"%s\">	
			<input type=\"hidden\" name=\"title\" value= \"%s\">	
			<input type=\"submit\" value=\"view\">
			</form>",$row["id"],$row["title"]
		);
		
		if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
			printf(
				"<form method=\"POST\" action=\"news_comment.php\">
				<input type=\"hidden\" name=\"story_id\" value= \"%s\">	
				<input type=\"hidden\" name=\"author_id\" value= \"%s\"> 
				<input type=\"submit\" value=\"comment\">
				</form>",$row["id"],$row["author_id"]
			);
			
			if(($row["author_id"]==$_SESSION['userid'])||($_SESSION['username']=='admin')){
				printf( 
					"<form METHOD=\"POST\" action=\"news_delete.php\">
					<input type=\"hidden\" name=\"story_id\" value= \"%s\">
					<input type=\"submit\" value=\"delete\">
					</form>",$row["id"]
				);
				printf( 	
					"<form METHOD=\"POST\" action=\"news_content_edit.php\">
					<input type=\"hidden\" name=\"story_id\" value= \"%s\">
					<input type=\"hidden\" name=\"title\" value= \"%s\"> 
					<input type=\"hidden\" name=\"story\" value= \"%s\">  
					<input type=\"submit\" value=\"edit\">
					</form>",$row["id"],$row["title"],$row["story"]
				);
			}
		}
		
		echo "</li>\n";
	}
	echo "</ul>";
	
	// Post news
	// CSRF Token
	if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
		echo '<br />';
		printf(
		"<form method=\"post\" action=\"news_post.php\">
		<input type=\"hidden\" name=\"token\" value=%s />
		<input type=\"submit\" value=\"Post a piece of News!\">
		</form>",
		$_SESSION['token']);
	}
	$stmt->close();
?>
	</div>
</body>
</html>