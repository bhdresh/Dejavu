<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

function NewUser($Username, $Role, $Email, $Status,$password)
{
	$hash = password_hash($password, PASSWORD_DEFAULT);
	$mysqli = db_connect();

	
	$createdDate = date("Y-m-d H:i:s");

	$sql = "INSERT INTO Users (Username, `Role`, Email, `Status`, `Timestamp`,`Password`)
	VALUES ('$Username', '$Role', '$Email','$Status','$createdDate','$hash')";

	if ($mysqli->query($sql) === TRUE) {
	//echo "New record created successfully";
	} else {
	//echo "Error: " . $sql . "<br>" . $conn->error;
	}

	//$stmt = $mysqli->prepare("Insert Into Users (Username, Role, Email, Status, Timestamp) VALUES (?,?,?,?,?)");
	//print_r($stmt);

	//exit;
	/*if (!$stmt) {
		echo "SFSDFSDF";
		exit;
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("sssss", $Username, $Role, $Email, $Status, $createdDate);

	$stmt->execute();

	$stmt->close();*/

}

function ModifyUser($Username, $Role, $Email, $Status, $alert_id, $pass)
{
	$mysqli = db_connect();

	$updatedDate = date("Y-m-d H:i:s");

	
	if($pass == '') {
	$stmt = $mysqli->prepare("Update Users SET Username = ?, Role = ?, Email = ?, Status =?, Timestamp = ? where id= ? ");
	
	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sssssi", $Username, $Role, $Email, $Status, $updatedDate, $alert_id);

	}
	else {
		$hash = password_hash($pass, PASSWORD_DEFAULT);
		$stmt = $mysqli->prepare("Update Users SET Username = ?, Role = ?, Email = ?, Status =?, Timestamp = ?, Password = ? where id= ? ");

		if (!$stmt) {
			throw new Exception('Error in preparing statement: ' . $mysqli->error);
		}
	
		$stmt->bind_param("ssssssi", $Username, $Role, $Email, $Status, $updatedDate, $hash, $alert_id);
	

	}
	
	$stmt->execute();

	$stmt->close();
}

function DisableUser($alert_id)
{
	$mysqli = db_connect();

	
	$stmt = $mysqli->prepare("Delete from Users where id= ? ");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("s", $alert_id);

	$stmt->execute();

	$stmt->close();
}

function ViewUser()
{
	$mysqli = db_connect();

	$user_id = $_SESSION['user_id'];

	$stmt = $mysqli->prepare("SELECT ID, Email, Username,CASE WHEN `Role` = 'admin' THEN 'Admin' ELSE 'User' END AS  `Role`, CASE WHEN `Status` = '1' THEN 'Active' ELSE 'Inactive' END AS `Status` FROM Users ");
	
	//$stmt->bind_param("s", $user_id);

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

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{

	if(isset($_REQUEST["action"]))
	{
		if($_POST["action"]=='modify')
		{
			$Username = $_POST["Username"];
			$Role = $_POST["Role"];
			$Email = $_POST["Email"];
			$Status = $_POST["Status"];
			$alert_id = $_POST["alert_id"];
			$pass = $_POST["Pass"];
			ModifyUser($Username, $Role, $Email, $Status, $alert_id, $pass);
		}

		if($_POST["action"]=='add')
		{
			$Username = $_POST["Username"];
			$Role = $_POST["Role"];
			$Email = $_POST["Email"];
			$Status = $_POST["Status"];
			$Pass = $_POST["Pass"];

			NewUser($Username, $Role, $Email, $Status,$Pass);
		}
		if($_POST["action"]=='disable')
		{
			$alert_id = $_POST["alert_id"];
			DisableUser($alert_id);
		}

	}
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	$event = ViewUser();
}

require 'manageUsersViews.php';

?>
