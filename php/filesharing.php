<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<title>File Sharing Site</title>
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
		// display the files
		printf( "<h2>Files of %s <a href=\"logout.php\">Log out</a> </h2>",
			htmlentities($_SESSION['username']));
		
		$path = '/home/hyfan/userfiles/'.$_SESSION['username'];
		$files = array_diff(scandir($path), array('.', '..'));
		
		// display files with function buttons
		echo '<ul>';
		foreach($files as $number => $filename){
			echo '<li>';
			printf("<h3>%s</h3>",htmlentities($filename));
			
			printf("
			<form action=\"filehandler.php\" method=\"GET\">
			<input type=\"hidden\" name=\"filename\" value=\"%s\"/>
			<input type=\"submit\" name=\"action\" value=\"Download\" />
			<input type=\"submit\" name=\"action\" value=\"Delete\" />
			<input type=\"submit\" name=\"action\" value=\"Share\" /> |
			<input type=\"text\" name=\"newfilename\" size=\"10\"/>
			<input type=\"submit\" name=\"action\" value=\"Rename\" />
			</form>
			",htmlentities($filename));
		
			echo '</li>';
		}
		echo '</ul>';
		
		echo '<h2><a href="upload.php">Upload</a></h2>';
	}
?>

</body>
</html>