<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

function SearchQuery()
{
	$mysqli = db_connect();
		
	
	$stmt = $mysqli->prepare("SELECT id, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp FROM Alerts where Status=1 ORDER BY LogInsertedTimeStamp DESC;");
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	//$arr = $result->fetch_assoc();

	while($row = $result->fetch_array()) {

  	$event[] = $row;
	
	}

	$stmt->close();

	return $event;
}

function DisableAlert($alert_id)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Update Alerts SET status = 0 where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("i", $alert_id);

	$stmt->execute();

	$stmt->close();
}


function removeAlerts()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Update Alerts SET status = 0");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->execute();

	$stmt->close();
}


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin'){

	if($_POST["action"]=='disable')
	{
		$alert_id = $_POST["alert_id"];

		DisableAlert($alert_id);
	}

	if($_POST["delete"]=='delete_all')
	{
		removeAlerts();
	}

	$event = SearchQuery();

	require 'eventsView.php';
}
else {
	header('location:loginView.php');
}

?>
