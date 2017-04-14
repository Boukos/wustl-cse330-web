<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);

// Content of database.php
$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'calendar');
if($mysqli->connect_errno) {
	echo json_encode(array(
		"success" => false,
		"message" => "Connection Failed: %s\n", $mysqli->connect_error
	));
	exit;
}

session_start();
if( !isset($_SESSION['userid'])) {
	echo json_encode(array(
		"success" => false,
		"message" => "Not logged in"
	));
	exit;
}

// CSRF
if($_SESSION['token'] !== $_POST['token']){ 
	echo json_encode(array(
		"success" => false,
		"message" => "Request forgery detected"
	));
	die();
}

if(!preg_match('/^[\w_\.\-]+$/',$_POST['content']) ){ 
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid content"
	));
	exit;
}

if(!isset($_POST['time'])){ 
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid time"
	));
	exit;
}

if(!preg_match('/^[\w_\.\-]+$/',$_POST['tag_id']) ){
	echo json_encode(array(
		"success" => false,
		"message" => "Invalid tag id"
	));
	exit;
}

$user_id = $_SESSION['userid'];

$content = $_POST['content'];
$time = $_POST['time'];
$tag_id = $_POST['tag_id'];
$event_id = $_POST['event_id'];

$stmt = $mysqli->prepare("UPDATE events SET content=?,time=?,tag_id=? WHERE event_id=?");
if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}
$stmt->bind_param('ssss',$content,$time,$tag_id,$event_id);
$stmt->execute();
$stmt->close();

echo json_encode(array(
	"success" => true,
	"message" => "Edit event success"
));
exit;

?>
