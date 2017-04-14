<?php
	if(empty($_POST)){
		echo 'Error, no POST variables.';
		exit;
	}
	else{
		$petname = $_POST['petname'];
		$username = $_POST['username'];
		$species = $_POST['species'];
		$weight=float($_POST['weight']);
		$description=$_POST['description'];
		$picture=$_POST['picture'];
		
		require 'database.php';
		$stmt = $mysqli->prepare("insert into pets (username, species, petname, weight, description, filename) values (?, ?, ?, ?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		// CSRF
		//if($_SESSION['token'] !== $_POST['token']){
		//	die("Request forgery detected");
		//}
		$stmt->bind_param('ssssss', $username, $species, $petname, $weight, $description, $picture);
		$stmt->execute(); 
		$stmt->close();
		
		// picture
		$filename=$filename = basename($_FILES['picture']['name']);
		$full_path = sprintf("/home/hyfan/userfiles/%s/%s", $username, $filename);
 
		if( move_uploaded_file($_FILES['picture']['tmp_name'], $full_path) ){
			printf("Uploaded %s.",$filename);
			printf("<a href=\"pet-listings.php\">Return</a>");
		}else{
			printf("Cannot upload %s.",$filename);
			printf("<a href=\"pet-listings.php\">Return</a>");
		}
	}
?>