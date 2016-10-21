<!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>Matches</title>
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
img.displayed{
	display: block;
    max-width:300px;
	margin-left: auto;
	margin-right: auto;
}
h1{
	text-align:center;
	text-decoration:underline;
}
p{
	text-align:center;
}
</style>
</head>
<body><div id="main">
<h1>Matches!</h1>

<ul>
<?php
	require 'database.php';
	$stmt=$mysqli->prepare("SELECT * FROM users");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	// $stmt->bind_param('s', $parameter); // ? as placeholder
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

	<form action = "age-range.php" method = "post" enctype="multipart/form-data" >
		Low: <input type = "number" name="low">
		High: <input type = "number" name="high">
		<br><br>
		<input type ="submit" name="submit" value="submit"/>
	</form>
	<br>
	<a href="create-profile.html">Create profile.</a>
	
</div></body>
</html>