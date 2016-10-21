<?php
require 'database.php';
if( !isset($_POST['username']))
{ echo " username could not be empty!";
exit;
}
if( !isset($_POST['email']))
{ echo " email could not be empty!";
exit;
}
if( !isset($_POST['age']))
{ echo " age could not be empty!";
exit;
}
if( !isset($_POST['description']))
{ echo " description could not be empty!";
exit;
}

// Get the filename and make sure it is valid
$filename = basename($_FILES['uploadedfile']['name']);
if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
	echo "Invalid filename";
	exit;
}
 
$full_path = sprintf("/home/hyfan/public_html/upload/%s", $filename);
 
if( move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){
	printf("Successfully uploaded %s.",$filename);
	printf("<a href=\"create-profile.html\">Return to create profile</a>");
}else{
	printf("Failed to upload %s.",$filename);
	printf("<a href=\"create-profile.html\">Return to create profile.</a>");
	// DIE DIE DIE
	die();
}

	require 'database.php';
	$username = $mysqli->real_escape_string($_POST['username']);
	$email = $mysqli->real_escape_string($_POST['email']);
	$age = $mysqli->real_escape_string($_POST['age']);
	$description = $mysqli->real_escape_string($_POST['description']);
	
	$stmt = $mysqli->prepare("insert into users (name,email,age,description,pictureUrl) values (?, ?, ?, ?, ?)");
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	
	$stmt->bind_param('sssss', $username, $email, $age, $description, $filename);
	$stmt->execute(); 
	$stmt->close();

	// header("Location: mainlist.php");
	//echo ("")
	exit; 
	 

?>