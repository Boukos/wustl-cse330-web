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
$user_id=$_SESSION['userid'];

//$stmt = $mysqli->prepare("select time, tag_id, content from events order by time where user_id=? and time>=? and time<=?");
$stmt = $mysqli->prepare("select event_id,content,time,tag_id from events where user_id=$user_id");
if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}
$stmt->execute();
$stmt->bind_result($event_id,$content,$time,$tag_id);

$events = array();
while($stmt->fetch()){
	$event = array(
		"event_id" => $event_id,
		"content" => $content,
		"time" => $time,
		"tag_id" => $tag_id
	);
	array_push($events,$event);
}		
$stmt->close();

echo json_encode(array(
	"success" => true,
	"events" => $events
));
exit; 

?>