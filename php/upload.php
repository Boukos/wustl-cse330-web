<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>File Sharing Site Upload</title>
	<link rel="stylesheet" type="text/css" href="FileSharingSite.css" />
</head>

<body>
<?php
	session_start();
	// check login
	if(!isset($_SESSION['username'])){
		echo 'Please login.';
		echo '<a href="login.html">Return..</a>';
	}
	else{
	?>
	<form enctype="multipart/form-data" action="uploader.php" method="POST">
	<p>
		<input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
		<label for="uploadfile_input">Upload File:</label><br/>
		<input type="file" name="uploadedfile" id="uploadfile_input" />
	</p>
	<p>
		<input type="submit" value="Upload" />
	</p>
	</form><br/>
	<?php
		echo '<a href="filesharing.php">Return to file list</a>';
	}
	
?>
</body>
</html>