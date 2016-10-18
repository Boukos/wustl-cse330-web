<!DOCTYPE html>
<html>
<head>
	<title>CSE330 M2 FileList</title>
	<style type="text/css">
	form {
		display: inline-block;
	}
	</style>
</head>

<body>
<?php
	session_start();
	printf( "%s's filelist:",$_SESSION['username']) ;
	echo '<br>';
	$path = '/home/hyfan/user_files/'.$_SESSION['username'];
	$files = array_diff(scandir($path), array('.', '..'));
	foreach($files as $number => $filename){
		echo $filename;
		printf("
		<form action=\"downloader.php\" method=\"GET\">
		<input type=\"hidden\" name=\"filename\" value=\"%s\"/>
		<input type=\"submit\" value=\"Download\" />
		</form>",htmlentities($filename) );
		printf("
		<form action=\"deleter.php\" method=\"GET\">
		<input type=\"hidden\" name=\"filename\" value=\"%s\"/>
		<input type=\"submit\" value=\"Delete\" />
		</form>",htmlentities($filename) );
		printf("
		<form action=\"renamer.php\" method=\"GET\">
		<input type=\"hidden\" name=\"filename\" value=\"%s\"/>
		<input type=\"text\" name=\"newfilename\" size=\"10\"/>
		<input type=\"submit\" value=\"Rename\" />
		</form>",htmlentities($filename) );
		printf("
		<form action=\"sharer.php\" method=\"GET\">
		<input type=\"hidden\" name=\"filename\" value=\"%s\"/>
		<input type=\"submit\" value=\"Share..\" />
		</form>",htmlentities($filename) );
		echo '<br>';
	}
?>

<form enctype="multipart/form-data" action="uploader.php" method="POST">
	<p>
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<label for="uploadfile_input">Upload File:</label> 
		<input type="file" name="uploadedfile" id="uploadfile_input" />
	</p>
	<p>
		<input type="submit" value="Upload" />
	</p>
</form>

<br>
<a href="logout.php">Log out</a>

</body>
</html>