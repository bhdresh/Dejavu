<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

function NewUser($user)
{
	$mysqli = db_connect();

	// Get submitted data
	$username = $user->username;
	$role = $user->role;
	$email = $user->email;
	$status = $user->status;
	$password = $user->password;
	$cpassword = $user->cpassword;

	$totp_key = "";
	$auth_key = "";
	$Password = password_hash($password, PASSWORD_DEFAULT);
	$totp_key_timestamp = date('Y-m-d H:i:s');
	$mail_last_sent = date('Y-m-d H:i:s');

	$stmt = $mysqli->prepare("Insert Into Users (Username, Role, Email, Status,totp_key, auth_key, Password, totp_key_timestamp, mail_last_sent) VALUES (?,?,?,?,?,?,?,?,?)");
	
	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sssisssss", $username, $role, $email, $status, $totp_key, $auth_key, $Password, $totp_key_timestamp, $mail_last_sent);


	$stmt->execute();

	$stmt->close();
}

function ModifyUser($user)
{
	$mysqli = db_connect();

	// Get submitted data
	$username = $user->username;
	$role = $user->role;
	$email = $user->email;
	$status = $user->status;
	$id = $user->id;

	$stmt = $mysqli->prepare("Update Users SET Username = ?, Role = ?, Email = ?, Status =? where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("sssii", $username, $role, $email, $status, $id);

	$stmt->execute();

	$stmt->close();
}

function DisableUser($user_id,$status=0)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Update Users SET Status =? where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("si", $status,$user_id);

	$stmt->execute();

	$stmt->close();
}

function ViewUser()
{
	$mysqli = db_connect();

	$users = array();

	$stmt = $mysqli->prepare("SELECT * FROM Users");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return $users;
	}

	else{
		while($row = $result->fetch_array()) {

	  	$users[] = $row;
		
		}

		$stmt->close();

		return $users;
	}
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	if(isset($_REQUEST["action"]))
	{
		if($_POST["action"]=='modify')
		{
			$user = new stdClass();
			$user->id = $_POST['user_id'];
			$user->username = $_POST['username'];
			$user->role = $_POST['role'];
			$user->email = $_POST['email'];
			$user->status = $_POST['status'];

			ModifyUser($user);
		}

		if($_POST["action"]=='add')
		{
			$user = new stdClass();
			$user->username = $_POST['username'];
			$user->role = $_POST['role'];
			$user->email = $_POST['email'];
			$user->status = $_POST['status'];
			$user->password = $_POST['password'];
			$user->cpassword = $_POST['cpassword'];

			NewUser($user);
		}
		if($_POST["action"]=='statuschange')
		{
			$user_id = $_POST["user_id"];
			$status = 0;
			if($_POST["user_status"] === "1"){
				$status = 1;
			}
			DisableUser($user_id,$status);
		}
	}
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	$event = ViewUser();
}

if($_POST || $_GET) {
	header("Location: manageUsers.php");
} else {
	require 'manageUsersViews.php';
}
?>