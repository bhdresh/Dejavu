<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;                                   
use PHPMailer\PHPMailer\Exception;

$email = $_REQUEST['email'];
$pass = $_REQUEST['password'];
$org = $_REQUEST['org'];
$api_key = bin2hex(openssl_random_pseudo_bytes(25));
$user_id = bin2hex(openssl_random_pseudo_bytes(25));
//$activation_link = bin2hex(openssl_random_pseudo_bytes(25));


$totp_key = 'null';

// Insert user

$mysqli = db_connect();

$status = 1;

$createdDate = date("Y-m-d H:i:s");

$hash = password_hash($pass, PASSWORD_DEFAULT);

$role = 'admin';

//Check if user exists

$mysqli = db_connect();

$stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where Status=1;");

$stmt->execute();

$result = $stmt->get_result();
if($result->num_rows === 0) {
    $mysqli = db_connect();

    $stmt->close();

    $stmt = $mysqli->prepare("Insert Into Users (Username, user_id, auth_key, totp_key, Password, Role, Email, auth_key_timestamp, totp_key_timestamp, Status, activation_link) VALUES (?,?,?,?,?,?,?,?,?,?,?);");

    $stmt->bind_param("sssssssssis", $email, $user_id, $api_key, $totp_key, $hash, $role, $email, $createdDate, $createdDate, $status, $activation_link);

    $stmt->execute();

    $stmt->close();

    header('location:loginView.php?email=register');
    exit();
    
}

else
{
    header('location:loginView.php');
    
}

