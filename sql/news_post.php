<!DOCTYPE HTML>
<head>
	<title>News Post</title>
	<link rel="stylesheet" type="text/css" href="news.css">
</head>

<body>
	<h1>Post</h1>
	<div id="main">
	<h2>
	<?php
		session_start();
		printf("Post as %s: ",$_SESSION['username']);
	?>
	</h2>

	<form name="submit" action="news_post_handler.php" method= "POST">
		<table>
			<tr>
				<td>Title:</td>
				<td><input type="text" name="title"/></td>
			</tr>
			<tr>
				<td>Story:</td>
				<td><input type="text" name="story"/></td>
			</tr>
			<tr>
				<td>Link:</td>
				<td><input type="text" name="link"/></td>
			</tr>
		</table>

		<input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
		<input type="submit" value="submit"/>
	</form><br /><br />
	<a href="news_list.php">Return to News List</a>
	</div>
</body>
</html>