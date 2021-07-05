<?php 

include 'db.php';

function chkUser()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where Status=1;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		$user = 'no user';

		header("Location: setupView.php"); 

	}

	else
	{
		$user = 'user present';

		header("Location: loginView.php"); 
	}

}

chkUser();

?>