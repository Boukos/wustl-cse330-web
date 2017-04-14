<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>File Sharing Site Upload</title>
	<link rel="stylesheet" type="text/css" href="FileSharingSite.css" />
</head>

<body>
<p>
<?php
session_start();
 
// check login
if(!isset($_SESSION['username'])){
	echo 'Please login.';
	echo '<a href="login.html">Return..</a>';
	exit;
}
 
// check valid filename
// cse 330 wiki
$filename = basename($_FILES['uploadedfile']['name']);
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}
 
// check valid username
// cse 330 wiki
$username = $_SESSION['username'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}

// generate the full path of file 
$full_path = sprintf("/home/hyfan/userfiles/%s/%s", $username, $filename);
 
if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){
	printf("Uploaded %s.",$filename);
	printf("<a href=\"filesharing.php\">Return</a>");
}else{
	printf("Cannot upload %s.",$filename);
	printf("<a href=\"filesharing.php\">Return</a>");
}
?>

</p>
</body>

</html>