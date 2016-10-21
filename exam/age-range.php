<!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>Matchmaking Site - Users by Age</title>
<style type="text/css">
body{
	width: 760px; /* how wide to make your web page */
	background-color: teal; /* what color to make the background */
	margin: 0 auto;
	padding: 0;
	font:12px/16px Verdana, sans-serif; /* default font */
}
div#main{
	background-color: #FFF;
	margin: 0;
	padding: 10px;
}
</style>
</head>
<body><div id="main">
 
<!-- CONTENT HERE -->
<h1>Users in Age Range</h1>
<ul>
<?php

	$low = $_POST['low'];
	$high = $_POST['high'];
	
	require 'database.php';
	$stmt=$mysqli->prepare("SELECT * FROM users WHERE age BETWEEN ? AND ?");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->bind_param('ss', $low, $high); 
	$stmt->execute();
	
	// $stmt->bind_result($parameter);
	$result = $stmt->get_result();
	//while($stmt->fetch()) {
	while($row = $result->fetch_assoc()){
		printf("
			\t<li>
			<p>Username: <i>%s</i></p>
			<p><b>info:</b> %s age %s</p>
			<p>Desc: %s</p>
			",
			htmlspecialchars($row["name"]),
			htmlspecialchars($row["email"]),
			htmlspecialchars($row["age"]),
			htmlspecialchars($row["description"])
		);
		$filename = "/home/hyfan/public_html/upload/".$row["pictureUrl"];
		$filedisppath = "/~hyfan/upload/".$row["pictureUrl"];
		$fileinfo = pathinfo($filename);
		
		printf("<img class = \"displayed\" src = \"%s\" >",$filedisppath);
		
		printf("
			</li>\n
			<br>
			");
	}

	$stmt->close();
?>
</ul>
<br>
<a href="show-users.php">See all users.</a>

</div></body>
</html>