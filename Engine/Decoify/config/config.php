<?php

include 'db.php';

function CreateAdminUser($username, $password, $email)
{

	$mysqli = db_connect();

	$status = 1;

	$createdDate = date("Y-m-d H:i:s");

	$hash = password_hash($password, PASSWORD_DEFAULT);

	$role = admin;

	$stmt = $mysqli->prepare("Insert Into Users (Username, Password, Role, Email, Timestamp, Status) VALUES (?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssss", $username, $password, $role, $email, $createdDate, $status);

	$stmt->execute();

	$stmt->close();

}

$val = getopt(null, ["username:", "password:", "email:"]);

if($val['username'] != '' && $val['password'] != '' && $val['email'] != '')
{
	CreateAdminUser($val['username'], $val['password'], $val['email'] );
	echo "User Created!! Start DejaVu";
}
else
{
echo "example -- php config.php --username=<username> --password=<password> --email=<email>";
}

?>