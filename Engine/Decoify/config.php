<?php

function CreateAdminUser($username, $password, $email)
{

	$mysqli = mysqli_connect('localhost','dejavu','password','dejavu');

	$status = 1;

	$createdDate = date("Y-m-d H:i:s");

	$hash = password_hash($password, PASSWORD_DEFAULT);

	$role = 'admin';

	$stmt = $mysqli->prepare("Insert Into Users (Username, Password, Role, Email, Timestamp, Status) VALUES (?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssss", $username, $hash, $role, $email, $createdDate, $status);

	$stmt->execute();

	$stmt->close();

	//add default alert
	$stmt2 = $mysqli->prepare("Insert Into AlertConfig (Alert_Name, Alert_Desc, Alert_Info, Email_To, Created_Date, Updated_Date, Status) VALUES (?,?,?,?,?,?,?)");

	$alert_name = 'Default Alerts';
	$alert_desc = 'Sends all alerts';
	$alertDetails = '[{"filter":"all","alertCriteria":[{"criteria":"decoyIP","condition":"not_eq","search_data":""}]}]';
	$email_to = $email;
	$createdDate = date("Y-m-d H:i:s");
	$updatedDate = date("Y-m-d H:i:s");
	$status = 1;
	$Alert_Type = 'Default';

	$stmt2->bind_param("ssssssss", $alert_name, $alert_desc, $alertDetails, $email_to, $createdDate, $updatedDate,$status, $Alert_Type);

	$stmt2->execute();

	$stmt2->close();

}

$val = getopt(null, ["username:", "password:", "email:"]);

if($val['username'] != '' && $val['password'] != '' && $val['email'] != '')
{
	CreateAdminUser($val['username'], $val['password'], $val['email']);
	echo "User Created!! Start DejaVu";
}
else
{
echo "example -- php config.php --username=<username> --password=<password> --email=<email>";
}

//./php /Applications/MAMP/htdocs/Decoify/config.php --username=test2 --password=pass2 --email=testt@gmail.com

?>
