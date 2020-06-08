<?php

include 'db.php';

function createAdminUser($username, $password, $email)
{

	$mysqli = db_connect();

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
	$stmt2 = $mysqli->prepare("Insert Into AlertConfig (Alert_Name, Alert_Desc, Alert_Info, Email_To, Created_Date, Updated_Date, Status, Alert_Type) VALUES (?,?,?,?,?,?,?,?)");

	$alert_name = 'Default Alerts';
	$alert_desc = 'Sends all alerts';
	$alertDetails = '[{"filter":"all","alertCriteria":[{"criteria":"decoyIP","condition":"not_eq","search_data":""}]}]';
	$email_to = $email;
	$createdDate = date("Y-m-d H:i:s");
	$updatedDate = date("Y-m-d H:i:s");
	$status = 1;
	$alert_type = 'Default';

	$stmt2->bind_param("ssssssss", $alert_name, $alert_desc, $alertDetails, $email_to, $createdDate, $updatedDate,$status, $alert_type);

	$stmt2->execute();

	$stmt2->close();

	exec('sudo /usr/bin/openssl req -x509 -nodes -days 3652 -newkey rsa:2048 -keyout dejavu.key -out dejavu.crt -subj "/C=AE/ST=Abu Dhabi/L=Abu Dhabi/O=Dejavu Deception/OU=IT Security/CN=192.168.56.102"');
	exec('sudo /bin/mv -f dejavu.* /etc/ssl/certs/');

}

function addSMTP($smtp_hostname, $smtp_username, $smtp_password){

	$mysqli = db_connect();

	//add default alert
	$stmt2 = $mysqli->prepare("Insert Into SMTPDetails (Hostname, Username, Password, Status) VALUES (?,?,?,?)");

	$status = 1;

	$stmt2->bind_param("ssss", $smtp_hostname, $smtp_username, $smtp_password, $status);

	$stmt2->execute();

	$stmt2->close();
}

function updateInterface($ipad, $mask, $gateway){

	exec("sudo /bin/sh /var/log/data/setup.sh $ipad $mask $gateway");
}

function chkUser()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where Status=1;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		$user = 'no user';

		if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]))
                {

            	//create new admin user

		createAdminUser($_POST['username'], $_POST['password'], $_POST['email']);

		//add SMTP details

		if(isset($_POST["smtp_hostname"]) && isset($_POST["smtp_username"]) && isset($_POST["smtp_password"])){
				
				$smtp_hostname = $_POST["smtp_hostname"];
				$smtp_username = $_POST["smtp_username"];
				$smtp_password = $_POST["smtp_password"];

				if($smtp_hostname != '' && $smtp_username != '' && $smtp_password != '' ){

					addSMTP($smtp_hostname, $smtp_username, $smtp_password);
				
				}
		}



                //Update Interface
                if(isset($_POST["ipad"]) && isset($_POST["mask"]) && isset($_POST["gateway"])){

                if(val_ip($_POST["ipad"]) && val_ip($_POST["mask"]) && val_ip($_POST["gateway"]))
                            {
                              $ipad=$_POST["ipad"];
                              $mask=$_POST["mask"];
                              $gateway=$_POST["gateway"];
                            }
                            else{
                              echo "<script>
                              alert('Please enter valid interface details');
                              window.location.href='setupView.php';
                              </script>";
                              exit();
                            }

                updateInterface($ipad, $mask, $gateway);
            	}

		}

	else
	{
		$user = 'user present';

		header("Location: loginView.php"); 
	}

}

}

chkUser();


?>
