<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

function showFiles()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT decoyname FROM decoys");
	
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
	$event = showFiles();
	
}

require 'crumbDecoyView.php';

?>
