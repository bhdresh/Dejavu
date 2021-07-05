<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include '../db.php';

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');

$mysqli = db_connect();
$user_id = $_SESSION['user_id'];

if(isset($_REQUEST["startDate"]) && isset($_REQUEST["endDate"]))
{
	
	$startDate = $_REQUEST['startDate'];
	$endDate = $_REQUEST['endDate'];

	$startDate = (string)$startDate . ' 00:00:01';

	$endDate = (string)$endDate . ' 23:59:59';

	$stmt2 = $mysqli->prepare("SELECT Attacker_IP, EventType, count(EventType), max(timestamp)  FROM CloudLogs where (TimeStamp between ? and ?) AND user_id=? group by EventType, Attacker_IP");

	$stmt2->bind_param("sss", $startDate,$endDate, $user_id);
}

else
{
	$stmt2 = $mysqli->prepare("SELECT Attacker_IP, EventType, count(EventType), max(timestamp)  FROM CloudLogs where user_id=? group by EventType, Attacker_IP;");
	$stmt2->bind_param("s", $user_id);
}

$stmt2->execute();

$result2 = $stmt2->get_result();

if($result2->num_rows === 0) {
	
	$stmt2->close();

	return;
}

while($row2 = $result2->fetch_array()) {

	$event2[] = $row2;

}

$stmt2->close();


//echo json_encode($event1);

echo json_encode($event2);

?>

