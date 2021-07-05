<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

function chkLogin($username, $password)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where username=? and Status=1;");

	$stmt->bind_param("s", $username);
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		$login = 'fail';

		return $login;
	}

	else
	{
		while($row = $result->fetch_assoc()) {
			$event[] = $row;
		}

		//$event[0]['Password'] --> Hash from database

		if(password_verify($password, $event[0]['Password'])){
			// write user data into PHP SESSION
	    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(10));
            $_SESSION['user_name'] = $event[0]['Username'];
            $_SESSION['role'] = $event[0]['Role'];
            $_SESSION['user_is_logged_in'] = true;
			$login = 'pass';
			return $login;
		}
		else
		{
			$login = 'fail';
			return $login;
		}

	}

}

if(isset($_POST["username"]) && isset($_POST["password"])){
	$username = $_POST["username"];
	$password = $_POST["password"];

	$chklogin = chkLogin($username, $password);

	if($chklogin == 'pass')
	{
		header('location:add-server-decoys.php?api=success');
	}
	if($chklogin == 'fail')
	{
		header('location:loginView.php?pass=fail');
	}
}

?>
