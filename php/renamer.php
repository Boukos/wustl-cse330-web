<?php
session_start();
$filename = $_GET['filename'];
$newfilename = $_GET['newfilename'];
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}
if( !preg_match('/^[\w_\.\-]+$/', $newfilename) ){
	echo "Invalid new filename";
	exit;
}
$username = $_SESSION['username'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}
$full_path = sprintf("/home/hyfan/user_files/%s/%s",$username,$filename);
$new_full_path = sprintf("/home/hyfan/user_files/%s/%s",$username,$newfilename);
if(rename($full_path,$new_full_path)){
	printf("Successfully renamed %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}
else{
	printf("Failed to rename %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}

?>