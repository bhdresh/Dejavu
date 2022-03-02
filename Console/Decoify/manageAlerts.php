<?php

if(!isset($_SESSION)) 
{  
	session_start();
}
require_once('includes/common.php');

include 'db.php';

function NewAlert($alert_name, $alert_desc, $email_to, $alertDetails)
{

	$mysqli = db_connect();

	$user_id = $_SESSION['user_id'];

	$status = 1;

	$createdDate = date("Y-m-d H:i:s");

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Insert Into AlertConfig (Alert_Name, Alert_Desc, Alert_Info, Email_To, Created_Date, Updated_Date, Status, User_ID) VALUES (?,?,?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssssss", $alert_name, $alert_desc, $alertDetails, $email_to, $createdDate, $updatedDate,$status, $user_id);

	$stmt->execute();

	$stmt->close();

}

function ModifyAlert($alert_name, $alert_desc, $email_to, $alertDetails, $alert_id)
{
	$mysqli = db_connect();

	$user_id = $_SESSION['user_id'];

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Update AlertConfig SET Alert_Name = ?, Alert_Desc = ?, Alert_Info = ?, Email_To =?, Updated_Date = ? where id= ? and User_ID = ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sssssis", $alert_name, $alert_desc, $alertDetails, $email_to, $updatedDate, $alert_id, $user_id);

	$stmt->execute();

	$stmt->close();
}

function DisableAlert($alert_id)
{
	$mysqli = db_connect();

	$user_id = $_SESSION['user_id'];

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Update AlertConfig SET Updated_Date = ?, status = 0 where id= ? and User_ID= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sis", $updatedDate, $alert_id, $user_id);

	$stmt->execute();

	$stmt->close();
}

function ViewAlert()
{
	$mysqli = db_connect();

	$user_id = $_SESSION['user_id'];

	$stmt = $mysqli->prepare("SELECT id, Alert_Name, Alert_Desc, Alert_Info, Email_To, Updated_Date  FROM AlertConfig where Status=1 and User_ID= ?");
	
	$stmt->bind_param("s", $user_id);

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

/*
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
*/

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

if(isset($_SESSION['user_name']) && isAuthorized($_SESSION))
{
	$event = ViewAlert();
}

require 'manageAlertsViews.php';

?>
