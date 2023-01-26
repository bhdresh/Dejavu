<?php

if(!isset($_SESSION))
{
    session_start();
}

include 'db.php';
require 'includes/vendor/autoload.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

$user_id=$_SESSION['user_id'];
$remote_ip=$_SERVER["REMOTE_ADDR"];
$command=preg_replace("/[^a-z]/","",$_POST["action"]);
$submited_csrf_token = preg_replace("/[^0-9a-zA-Z]/","",$_POST["csrf_token"]);


	if($command == 'resetauthkey' && $_SESSION['csrf_token'] == $submited_csrf_token)
       	{
		$new_key = bin2hex(openssl_random_pseudo_bytes(25));
 		$mysqli = db_connect();
       		$stmt = $mysqli->prepare("Update Users set auth_key=?,auth_key_timestamp=current_timestamp WHERE user_id=?");
       		$stmt->bind_param("ss", $new_key, $user_id);
		$stmt->execute();
	}

	if($command == 'resettotpkey' && $_SESSION['csrf_token'] == $submited_csrf_token)
	{
		$authenticator = new PHPGangsta_GoogleAuthenticator();
		$totp_key = $authenticator->createSecret();
       		$mysqli = db_connect();
       		$stmt = $mysqli->prepare("Update Users set totp_key=?,totp_key_timestamp=current_timestamp WHERE user_id=?");
       		$stmt->bind_param("ss", $totp_key, $user_id);
		$stmt->execute();
	}

	header('location:list-key.php');
} else {
  header('location:loginView.php');
}


?>

