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
// read user list
$userlist = array();
$h = fopen("/home/hyfan/user_files/userlist.txt", "r");
$linenum = 1;
while( !feof($h) ){
	array_push($userlist,trim(fgets($h)));
}
fclose($h);

$distrib_flag = true;
foreach($userlist as $key => $value){
	if($value != $username){
		$full_path = sprintf("/home/hyfan/user_files/%s/%s",$username,$filename);
		$other_full_path = sprintf("/home/hyfan/user_files/%s/%s",$value,$filename);
		$distrib_flag = $distrib_flag && copy($full_path, $other_full_path);
	}
}

if($distrib_flag){
	printf("Successfully distributed %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}
else{
	printf("Failed to distribute %s.",$filename);
	printf("<a href=\"filelist.php\">Return to filelist</a>");
}

?>