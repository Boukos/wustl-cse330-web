<!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>Pet Listings</title>
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
<h1>Pet Listings</h1>
<?php
	require 'database.php';
	$stmt = $mysqli->prepare("select username,species,name,weight,description,filename from pets ");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt->execute();
	$result = $stmt->get_result();

	// List the pets
	echo "<ul>\n";
	while($row = $result->fetch_assoc()){
		echo "<li>";
		printf(
			"<b>%s</b> ", htmlspecialchars( $row["petname"] )
		);
		printf(
			"<br /> <i>%s</i> <a href= \"%s\">Link</a> <br /> %s %s \n",
			htmlspecialchars( $row["username"] ),
			htmlspecialchars( $row["description"] ),
			htmlspecialchars( $row["species"] ),
			htmlspecialchars( $row["weight"] )
		);
		echo "</li>\n";
	}
	echo "</ul>";
	
	if(isset($_SESSION['username']) && !empty($_SESSION['username'])){
		echo '<a href="add-pet.php">Add a new pet</a>';
	}
	
?>

</div></body>
</html>
