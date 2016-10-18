<?php
session_start();
$filename = $_GET['filename'];
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}
$username = $_SESSION['username'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}
$full_path = sprintf("/home/hyfan/user_files/%s/%s",$username,$filename);
if(unlink($full_path)){
	printf("Successfully deleted %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}
else{
	printf("Failed to delete %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}

?>