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

$stmt = $mysqli->prepare("select tag_name from tags");
if(!$stmt){
	echo json_encode(array(
		"success" => false,
		"message" => "Query Prep Failed: %s\n", $mysqli->error
	));
	exit;
}
$stmt->execute();
$stmt->bind_result($tag);

$tags = array();
while($stmt->fetch()){
	array_push($tags,$tag);
}		
$stmt->close();

echo json_encode(array(
	"success" => true,
	"tags" => $tags
));
exit; 
?>