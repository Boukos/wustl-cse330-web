<!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>Add Pet Listing</title>
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
h1{
	text-align: center;
}
</style>
</head>
<body><div id="main">
 
<!-- CONTENT HERE -->

<?php
	session_start();
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])){
		
	}
?>

<h1>Add New Pet</h1>
<p>Please enter the details about your pet below</p>

<form action="pet-submit.php" method="POST" enctype="multipart/form-data">
<input type="text" name="petname">
<input type="hidden" name="username">
<select>
	<option value="cat">cat</option>
	<option value="dog">dog</option>
	<option value="chinchilla">chinchilla</option>
	<option value="snake">snake</option>
	<option value="rabbit">rabbit</option>
</select>
<input type="number" name="weight" min="0">
<input type="textarea" name="description">
<input type="file" name="picture">
<input type="submit" value="submit">
</form>
 
</div></body>
</html>