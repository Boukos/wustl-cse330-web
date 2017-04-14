<?php
session_start();

// check login
if(!isset($_SESSION['username'])){
	echo 'Please login.';
	echo '<a href="login.html">Return..</a>';
	exit;
}
	
// check valid names
$username = $_SESSION['username'];
if( !preg_match('/^[\w_\-]+$/', $username) ){
	echo "Invalid username";
	exit;
}
$filename = $_GET['filename'];
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}

// download
if($_GET['action']=='Download'){
	$newfilename = 'null';
	$full_path = sprintf("/home/hyfan/userfiles/%s/%s",$username,$filename);
	$finfo = new finfo(FILEINFO_MIME_TYPE);
	$mime = $finfo->file($full_path);
	header("Content-Type: ".$mime);
	readfile($full_path);
}
// delete
else if($_GET['action']=='Delete'){
	$full_path = sprintf("/home/hyfan/userfiles/%s/%s",$username,$filename);
	if(unlink($full_path)){
		printf("Deleted %s.",$filename);
		printf("<a href=\"filesharing.php\">Return</a>");
	}
	else{
		printf("Cannot delete %s.",$filename);
		printf("<a href=\"filesharing.php\">Return</a>");
	}
}
// share, only for admin
else if($_GET['action']=='Share'){
	if($username != 'admin'){
		echo "No permission";
		exit;
	}
	else{
		// read user list
		$userlist = array();
		$h = fopen("/home/hyfan/userfiles/userlist.txt", "r");
		while( !feof($h) ){
			array_push($userlist,trim(fgets($h)));
		}
		fclose($h);

		$distrib_flag = true;
		foreach($userlist as $key => $value){
			if($value != $username){
				$full_path = sprintf("/home/hyfan/userfiles/%s/%s",$username,$filename);
				$other_full_path = sprintf("/home/hyfan/userfiles/%s/%s",$value,$filename);
				$distrib_flag = $distrib_flag && copy($full_path, $other_full_path);
			}
		}

		if($distrib_flag){
			printf("Distributed %s.",$filename);
			printf("<a href=\"filesharing.php\">Return</a>");
		}
		else{
			printf("Cannot distribute %s.",$filename);
			printf("<a href=\"filesharing.php\">Return</a>");
		}
	}
}
// rename
else if($_GET['action']=='Rename'){
	$newfilename = $_GET['newfilename'];
	if( !preg_match('/^[\w_\.\-]+$/', $newfilename) ){
		echo "Invalid new filename";
		exit;
	}
	$full_path = sprintf("/home/hyfan/userfiles/%s/%s",$username,$filename);
	$new_full_path = sprintf("/home/hyfan/userfiles/%s/%s",$username,$newfilename);
	if(rename($full_path,$new_full_path)){
		printf("Renamed %s to %s.",$filename,$newfilename);
		printf("<a href=\"filesharing.php\">Return</a>");
	}
	else{
		printf("Cannot rename %s.",$filename);
		printf("<a href=\"filesharing.php\">Return </a>");
	}
}

?>