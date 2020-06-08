<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';


function uploadFiles($file_name, $tmp_name)
{

	$filename = $file_name. '.zip';

	//This needs to be modified
	$target_dir = "/var/dejavufiles/uploads/";

	$target_file = $target_dir . $filename;

	
	if(move_uploaded_file($tmp_name, $target_file)) {

		//Add file name to database
		$mysqli = db_connect();

		$status = 1;

		$createdDate = date("Y-m-d H:i:s");

		$updatedDate = date("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("Insert Into FileDetails (file_name, status, created_date, updated_date) VALUES (?,?,?,?)");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("ssss", $filename, $status, $createdDate, $updatedDate);

		$stmt->execute();

		$stmt->close();

		return true;
	  
	}

	else{

		return false;
	}

}

function showFiles()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT id, file_name FROM FileDetails where Status=1");
	
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

function deletefile($file_id)
{
	$mysqli = db_connect();

	$updatedDate = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Update FileDetails SET Updated_Date = ?, status = 0 where id= ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("si", $updatedDate, $file_id);

	$stmt->execute();

	$stmt->close();

	return true;

}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	$event = showFiles();

	if(isset($_POST['file_name']))
	{
		$path = pathinfo($_FILES['fileToUpload']['name']);

		if($path['extension'] == 'zip' || 'rar')
		{
			$file_name = $_POST['file_name'];

			$tmp_name	   = $_FILES['fileToUpload']['tmp_name'];

			$uploadFile = uploadFiles($file_name, $tmp_name);

			if($uploadFile){

			$event = showFiles();

			header('location:addFiles.php?msg=success');
		
			exit();

			}

			else{

				$event = showFiles();
				
				header('location:addFiles.php?msg=fail');
			
				exit();

			}	

		}

		else
		{
			header('location:addFiles.php?msg=invalidfile');
			exit();
		}
		
	}

	if(isset($_GET['del_id']))
	{ 
		$file_id = $_GET['del_id'];

		if(deletefile($file_id))
		{
			header('location:addFiles.php');
		
			exit();
		}
	}
	
}

require 'addFilesViews.php';

?>
