<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
	if(isset($_POST["oldPassword"]) && isset($_POST["newPassword"])){
		$oldPassword = $_POST["oldPassword"];
		$newPassword = $_POST["newPassword"];

		updatePassword($oldPassword, $newPassword);
	}

	if(isset($_POST["smtp_hostname"]) && isset($_POST["smtp_username"]) && isset($_POST["smtp_password"])){

                $smtp_hostname = $_POST["smtp_hostname"];
                $smtp_username = $_POST["smtp_username"];
                $smtp_password = $_POST["smtp_password"];

                updateSMTP($smtp_hostname, $smtp_username, $smtp_password);
    }

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
	      window.location.href='updateSettings.php';
	      </script>";
	      exit();
	    }

		updateInterface($ipad, $mask, $gateway);
	}


	if(isset($_POST["addsyslog"])){
		$ip = $_POST["syslog_server_ip"];
                $port = array();
                preg_match('/[0-9]+/', $_POST["syslog_port"], $port);
                $sprotocol = array();
                preg_match('/[TU][CD]P/', $_POST["protocol"], $sprotocol);
	if(val_ip($ip) && $port[0] && $sprotocol[0]){
		
		exec("sudo /bin/cat /etc/rsyslog.d/magento-example.com.conf.org | sudo sed \"s/protocol:syslogip:port/$sprotocol[0]:$ip:$port[0]/g\" | sed \"s/TCP:/@@/g\" |sed \"s/UDP:/@/g\" > /etc/rsyslog.d/magento-example.com.conf" ,$outputsyslog,$resultsyslog);
		exec("sudo /etc/init.d/rsyslog restart",$outputsyslog1,$resultsyslog1);

		$mysqli = db_connect();

        	$stmt = $mysqli->prepare("SELECT id FROM SyslogDetails where Status='1';");

        	$stmt->execute();

        	$result = $stmt->get_result();

        	if(mysqli_num_rows($result) === 0) {

                	$stmt->close();

                	//Insert if no details

                	$mysqli = db_connect();

                	//add default alert
                	$stmt2 = $mysqli->prepare("Insert Into SyslogDetails (IP, PORT, Protocol, Status) VALUES (?,?,?,?)");

                	$status = 1;

                	$stmt2->bind_param("ssss", $ip, $port[0], $sprotocol[0], $status);

                	$stmt2->execute();

                	$stmt2->close();
		

        	}

        	else{
                	//Update if details present

                	$mysqli = db_connect();

                	//add default alert
                	$stmt2 = $mysqli->prepare("Update SyslogDetails Set IP = ? , PORT = ?, Protocol = ? Where Status = '1'");

                	$status = 1;

                	$stmt2->bind_param("sss", $ip, $port[0], $sprotocol[0]);

                	$stmt2->execute();

                	$stmt2->close();


        	}
	
	
	} else {
		exec("sudo /bin/cat /etc/rsyslog.d/magento-example.com.conf.org > /etc/rsyslog.d/magento-example.com.conf" ,$outputsyslog2,$resultsyslog2);
                exec("sudo /etc/init.d/rsyslog restart",$outputsyslog3,$resultsyslog3);

                $mysqli = db_connect();

                $stmt3 = $mysqli->prepare("DELETE FROM SyslogDetails where Status='1';");

                $stmt3->execute();

		$stmt3->close();

	}

		if($_POST["SyslogServer"] == 'on')
        	{
			exec("sudo /sbin/ifconfig eth0| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
			$ipa=$output1[0];
			exec("sudo /bin/cat /etc/rsyslog.d/00-custom.conf.org | sudo sed \"s/serverip/$ipa/g\" > /etc/rsyslog.d/00-custom.conf" ,$outputsyslog,$resultsyslog);
                	exec("sudo /etc/init.d/rsyslog restart",$outputsyslog5,$resultsyslog5);
			$mysqli = db_connect();
        		$stmt = $mysqli->prepare("SELECT serverstatus FROM SyslogServerDetails;");
        		$stmt->execute();
        		$result = $stmt->get_result();
        		if(mysqli_num_rows($result) === 0) {

                		$stmt->close();

                		//Insert if no details

                		$mysqli = db_connect();

                		//add default alert
                		$stmt2 = $mysqli->prepare("Insert Into SyslogServerDetails (serverstatus) VALUES (?)");

                		$status = 1;

                		$stmt2->bind_param("s", $status);

                		$stmt2->execute();

                		$stmt2->close();
		

        		} else {

                		//Update if details present

                		$mysqli = db_connect();

                		//add default alert
                		$stmt2 = $mysqli->prepare("Update SyslogServerDetails set serverstatus = ?");

                		$status = 1;

                		$stmt2->bind_param("s", $status);

                		$stmt2->execute();

                		$stmt2->close();

        		}
	
		} else {

			exec("sudo /bin/cat /etc/rsyslog.d/00-custom.conf.org > /etc/rsyslog.d/00-custom.conf" ,$outputsyslog2,$resultsyslog2);
                	exec("sudo /etc/init.d/rsyslog restart",$outputsyslog3,$resultsyslog3);
                	$mysqli = db_connect();
                	$stmt3 = $mysqli->prepare("DELETE FROM SyslogServerDetails;");
                	$stmt3->execute();
			$stmt3->close();
		}	

	header('location:updateSettingsView.php?smtp=success');
        exit();	
	}



	if(isset($_POST["backup"]) && isset($_POST["backup"]) == '1')
	{
		downloadFile();
	}

	if(isset($_POST["reboot"]) && isset($_POST["reboot"]) == '1')
	{
		reboot();
	}

	if(isset($_POST['file_true']))
	{
		$path = pathinfo($_FILES['fileToUpload']['name']);

		if($path['extension'] == 'zip')
		{
			$file_name = 'configbackup.zip';

			$tmp_name	   = $_FILES['fileToUpload']['tmp_name'];

			$uploadFile = uploadFiles($file_name, $tmp_name);

		}

		else
		{
			header('location:updateSettingsView.php?msg=invalidfile');
			exit();
		}
		
	}

	header('location:updateSettingsView.php');
	exit();

}

else{
	header('location:updateSettingsView.php');
	exit();
}

function downloadFile()
{
	/* File to download
	1) Entire DB
	2) /etc/network/interfaces
	3) /etc/apache2/port.conf
	4) /etc/ssh/sshd_config
	*/

	//sudo apt-get install zip

	exec("sudo /bin/rm -r /var/dejavufiles/backup");

	exec("sudo /bin/mkdir /var/dejavufiles/backup");

	exec("sudo /bin/chmod 777 /var/dejavufiles/backup");

	exec("sudo /bin/cp -f /etc/network/interfaces /var/dejavufiles/backup/interfaces");
	exec("sudo /bin/cp -f /etc/apache2/ports.conf /var/dejavufiles/backup/ports.conf");
	exec("sudo /bin/cp -f /etc/ssh/sshd_config /var/dejavufiles/backup/sshd_config");

	exec("sudo /bin/cp -r /var/dejavufiles/uploads /var/dejavufiles/backup");
	//backuping up current database

	exec("sudo mysqldump -u root dejavu > /var/dejavufiles/backup/db.sql");

	//zip the whole backup folder
	exec("cd /var/dejavufiles && sudo zip -r backup.zip backup");

	exec("sudo /bin/mv -f /var/dejavufiles/backup.zip /var/www/html/Decoify/backup.zip");

	//exec("sudo /bin/rm -r /var/dejavufiles/backup");

	header('location:backup.zip'); 
	
	exit();

}

function uploadFiles($file_name, $tmp_name)
{
	$filename = $file_name;

	//This needs to be modified
	$target_dir = "/var/dejavufiles/configbackup/";

	$target_file = $target_dir . $filename;

	
	if(move_uploaded_file($tmp_name, $target_file)) {

		exec("sudo unzip -o /var/dejavufiles/configbackup/configbackup.zip -d /var/dejavufiles/configbackup/");

		$interface = exec("sudo /bin/cat /var/dejavufiles/configbackup/backup/interfaces | grep -i \"address\" | cut -d \" \" -f 2");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/interfaces /etc/network/interfaces");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/ports.conf /etc/apache2/ports.conf");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/sshd_config /etc/ssh/sshd_config");
		
		//exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/uploads /var/dejavufiles/uploads");
		//restoring db

		exec("sudo mysql -u root dejavu < /var/dejavufiles/configbackup/backup/db.sql");
		
		exec("sudo rm -r /var/dejavufiles/configbackup/*");		
		$location = "location:updateSettingsView.php?config=success&interface=". $interface; 

		header($location);
		
		exit();
	}
}

function updateInterface($ipad, $mask, $gateway){

	exec("sudo /bin/sh /var/log/data/setup.sh $ipad $mask $gateway");	
	exit();
}

function reboot()
{
	exec("sudo reboot");	
	exit();
}

function updateSMTP($smtp_hostname, $smtp_username, $smtp_password){


	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT id  FROM SMTPDetails;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
	
		$stmt->close();

		//Insert if no details

		$mysqli = db_connect();

		//add default alert
		$stmt2 = $mysqli->prepare("Insert Into SMTPDetails (Hostname, Username, Password, Status) VALUES (?,?,?,?)");

		$status = 1;

		$stmt2->bind_param("ssss", $smtp_hostname, $smtp_username, $smtp_password, $status);

		$stmt2->execute();

		$stmt2->close();

		header('location:updateSettingsView.php?smtp=success');
		
		exit();

	}

	else{
		//Update if details present

		$mysqli = db_connect();

		//add default alert
		$stmt2 = $mysqli->prepare("Update SMTPDetails Set Hostname = ? , Username = ?, Password = ? Where Status = 1");

		$status = 1;

		$stmt2->bind_param("sss", $smtp_hostname, $smtp_username, $smtp_password);

		$stmt2->execute();

		$stmt2->close();

		header('location:updateSettingsView.php?smtp=success');

		exit();
	}

}


function updatePassword($oldPassword, $newPassword)
{
	$username = $_SESSION['user_name'];


		//Check Old Password

		$mysqli = db_connect();

		$stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where username=? and Status=1;");

		$stmt->bind_param("s", $username);
		
		$stmt->execute();

		$result = $stmt->get_result();
		
		if($result->num_rows === 0) {
			
			$stmt->close();
			header('location:updateSettingsView.php?pass=fail');
			exit();
		}

		else
		{
			while($row = $result->fetch_assoc()) {
				$event[] = $row;
			}

			//$event[0]['Password'] --> Hash from database

			if(password_verify($oldPassword, $event[0]['Password'])){

				$mysqli = db_connect();

				$updatedDate = date("Y-m-d H:i:s");

				$stmt = $mysqli->prepare("Update Users SET Password = ? where Username= ?");

				if (!$stmt) {
			    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
				}

				$hash = password_hash($newPassword, PASSWORD_DEFAULT);

				$stmt->bind_param("ss", $hash, $username);

				$stmt->execute();

				$stmt->close();

				header('location:updateSettingsView.php?pass=success');
				exit();
	          
			}
			else
			{
				header('location:updateSettingsView.php?pass=fail');
				exit();
			}

		}
}

?>
