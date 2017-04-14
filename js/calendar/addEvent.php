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
$timestamp = $_POST['time'];
$tag_id = $_POST['tag_id'];
$html_safe_content = htmlentities($content);
$html_safe_timestamp = htmlentities($timestamp);
$html_safe_tag_id = htmlentities($tag_id);

$stmt = $mysqli->prepare("insert into events (user_id, content, time, tag_id) values (?, ?, ?, ?)");

if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}
 
$stmt->bind_param('ssss', $user_id, $content, $timestamp, $tag_id);
$stmt->execute();
$stmt->close();

echo json_encode(array(
	"success" => true,
	"message" => "Add event success"
));
exit;