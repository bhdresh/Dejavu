<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

function NewAlert($alert_name, $alert_desc, $email_to, $alertDetails)
{

	$mysqli = db_connect();

	$status = 1;

	$createdDate = date("Y-m-d H:i:s");

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Insert Into AlertConfig (Alert_Name, Alert_Desc, Alert_Info, Email_To, Created_Date, Updated_Date, Status) VALUES (?,?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("sssssss", $alert_name, $alert_desc, $alertDetails, $email_to, $createdDate, $updatedDate,$status);

	$stmt->execute();

	$stmt->close();

}

function ModifyAlert($alert_name, $alert_desc, $email_to, $alertDetails, $alert_id)
{
	$mysqli = db_connect();

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Update AlertConfig SET Alert_Name = ?, Alert_Desc = ?, Alert_Info = ?, Email_To =?, Updated_Date = ? where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sssssi", $alert_name, $alert_desc, $alertDetails, $email_to, $updatedDate, $alert_id);

	$stmt->execute();

	$stmt->close();
}

function DisableAlert($alert_id)
{
	$mysqli = db_connect();

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Update AlertConfig SET Updated_Date = ?, status = 0 where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("si", $updatedDate, $alert_id);

	$stmt->execute();

	$stmt->close();
}

function ViewAlert()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT id, Alert_Name, Alert_Desc, Alert_Info, Email_To, Updated_Date  FROM AlertConfig where Status=1 and Alert_Type IS NULL;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return $event;
	}

	else{
		while($row = $result->fetch_array()) {

	  	$event[] = $row;
		
		}

		$stmt->close();

		return $event;
	}
}

function DefaultAlert()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT id, Alert_Name, Alert_Desc, Alert_Info, Email_To, Updated_Date  FROM AlertConfig where Status=1 and Alert_Type = 'default';");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return $default_event;
	}

	else{
		while($row = $result->fetch_array()) {

	  	$default_event[] = $row;
		
		}

		$stmt->close();

		return $default_event;
	}
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{

	if(isset($_REQUEST["action"]))
	{
		if($_POST["action"]=='modify')
		{
			$alertDetails = $_POST["jsonSearchString"];
			$email_to = $_POST["email_to"];
			$alert_name = $_POST["alert_name"];
			$alert_desc = $_POST["alert_desc"];
			$alert_id = $_POST["alert_id"];
			ModifyAlert($alert_name, $alert_desc, $email_to, $alertDetails, $alert_id);
		}

		if($_POST["action"]=='add')
		{
			$alertDetails = $_POST["jsonSearchString"];
			$email_to = $_POST["email_to"];
			$alert_name = $_POST["alert_name"];
			$alert_desc = $_POST["alert_desc"];

			NewAlert($alert_name, $alert_desc, $email_to, $alertDetails);
		}
		if($_POST["action"]=='disable')
		{
			$alert_id = $_POST["alert_id"];
			DisableAlert($alert_id);
		}

	}
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	$event = ViewAlert();
	$default_event = DefaultAlert();
}

require 'manageAlertsViews.php';

?>