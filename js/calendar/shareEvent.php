<?php
if(empty($_SESSION))
{
	session_start();
}
include 'function.php';

header("Content-Type: application/json");
if(isset($_POST['sharedusername'])){
	
	//$user_id = $_POST['shared_user_id'];
	$user = $_POST['sharedusername'];
	
	$user =  usernameCheck($user);
    if ($user == "Invalid username"){
    	echo json_encode(array(
		"success"=>false,
		"message"=>"invalid user name!"
		));
		exit;
    }
	//else
		
	if(!checkUser($user)){
		echo json_encode(array(
		"success"=>false,
		"message"=>"user not exists!"
		));
		exit;
	}
	else{
		$content = $_POST['content'];
		$timestamp = $_POST['timestamp'];
		$username = $user;//$_SESSION['sharedusername'];
		$tag_id = $_POST['tag_id'];
		$user_id = getUserId($username);

		$addEventResult = addEvent($user_id,$content,$timestamp,$tag_id);
		if($addEventResult!='Success') {
			echo json_encode(array(
					"success"=>false,
					"message"=>$addEventResult));
		}
		else{
			echo json_encode(array(
					"success"=>true,
					"message"=>"success"));
			}
	}
}
else{
	echo json_encode(array(
		"success"=>false,
		"message"=>"please specify user and event"
		));
	exit;
}
?>
