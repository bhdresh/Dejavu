<?php
require 'includes/vendor/autoload.php';
//Access-Control-Allow-Origin header with wildcard.
header('Access-Control-Allow-Origin: *');

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';


function chkLogin($username, $password)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT * FROM Users where username=? and Status=1;");

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
		$totp_key = $event[0]["totp_key"];
	    //$result="";		
	    //$output="";
	   // exec("sudo /usr/bin/oathtool --totp=sha512 -w 5 --base32 $totp_key $OTP",$output,$result);
	   // if ($output[0] < 0 || $output[0] > 3 || $result != 0)
	   // {
		//   $login = 'fail';
                 //  return $login;

	    // }

		$_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(10));
		$_SESSION['user_name'] = $event[0]['Username'];
		$_SESSION['role'] = $event[0]['Role'];
		$_SESSION['user_is_logged_in'] = true;
	    $_SESSION['user_id'] = $event[0]['user_id'];	

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


function chkApiLogin($api_key)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT * FROM Users where auth_key=? and Status=1;");

	$stmt->bind_param("s", $api_key);
	
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

		//Set Session Values
		$_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(10));
		$_SESSION['user_name'] = $event[0]['Username'];
		$_SESSION['role'] = $event[0]['Role'];
		$_SESSION['user_is_logged_in'] = true;
	    $_SESSION['user_id'] = $event[0]['user_id'];		

		$login = 'pass';

		return $login;
		
	}

}


if(isset($_POST["username"]) && isset($_POST["password"])){

	$username = $_POST["username"];
	$password = $_POST["password"];
	$OTP = preg_replace("/[^0-9]/","",$_POST["OTP"]);
	
	$chklogin = chkLogin($username, $password);

	if($chklogin == 'pass')
	{
		$mysqli = db_connect();

		$stmt = $mysqli->prepare("SELECT ID FROM CloudLogs where user_id=?;");
		$stmt->bind_param("s", $_SESSION['user_id']);	
		$stmt->execute();

		$result = $stmt->get_result();
		
		if($result->num_rows === 0) {
		
		$stmt->close();

		//no logs

			header('location:list-key.php');

		}

		else{
			header('location:events.php');
		}

		
	}
	if($chklogin == 'fail')
	{
		header('location:loginView.php?pass=fail');
	}
}


if(isset($_POST["api_key"])){

	$api_key = $_POST["api_key"];
	
	$chklogin = chkApiLogin($api_key);

	

	if($chklogin == 'pass')
	{
		$mysqli = db_connect();
		$stmt = $mysqli->prepare("SELECT ID FROM CloudLogs where user_id=?;");
                $stmt->bind_param("s", $_SESSION['user_id']);
		$stmt->execute();

		$result = $stmt->get_result();
		
		if($result->num_rows === 0) {
		
		$stmt->close();

		

		//no logs

			header('location:list-key.php');

		}

		else{

			header('location:events.php');
		}

		
	}
	if($chklogin == 'fail')
	{
		header('location:loginView.php?pass=fail');
	}
}

?>
