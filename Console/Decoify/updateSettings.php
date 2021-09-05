<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION)) 
{  
	session_start();
}

if(!isset($_SESSION['user_name']) && $_SESSION['role'] != 'admin')
{
	header('location:loginView.php');
	exit();

}

if($_SESSION['csrf_token'] != $_REQUEST['csrf_token'])
{
        header('location:loginView.php');
        exit();

}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{

    if(isset($_POST["oldPassword"]) && isset($_POST["newPassword"])){
		$oldPassword = $_POST["oldPassword"];
		$newPassword = $_POST["newPassword"];

		updatePassword($oldPassword, $newPassword);
    }


    
    if(isset($_POST["smtp_hostname"])){

		$PortNumber == 25; //Default Value if not entered

        $smtp_hostname = $_POST["smtp_hostname"];
        $smtp_username = $_POST["smtp_username"];
		$smtp_password = $_POST["smtp_password"];
		$PortNumber = $_POST["PortNumber"];
		$From_Email = $_POST["From_Email"];

        updateSMTP($smtp_hostname, $smtp_username, $smtp_password, $PortNumber, $From_Email);
	}
	if(isset($_POST["test_email"]))
	{
		$test_emailaddress = $_POST['test_emailaddress']; 
		testEmail($test_emailaddress);
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


	if(isset($_POST["backup"]) && isset($_POST["backup"]) == '1')
	{
		downloadFile();
	}



	if(isset($_POST['file_true']))
	{
		$path = pathinfo($_FILES['fileToUpload']['name']);

		if($path['extension'] == 'zip')
		{
			$file_name = 'configbackup.zip';

			$tmp_name  = $_FILES['fileToUpload']['tmp_name'];

			$uploadFile = uploadFiles($file_name, $tmp_name);

		}

		else
		{
			header('location:backupSettings.php?msg=invalidfile');
			exit();
		}
		
	}


	if(isset($_POST["reboot"]) && $_POST["reboot"] == '1' && $_SESSION['csrf_token'] == $_POST['csrf_token'])
	{
		reboot();
	}
	if(isset($_POST["shutdown"]) && $_POST["shutdown"] == '1' && $_SESSION['csrf_token'] == $_POST['csrf_token'])
	{
			shutdown();
	}


	if(isset($_POST["reset"]) && $_POST["reset"] == '1' && $_SESSION['csrf_token'] == $_POST['csrf_token'])
	{
			devicereset();
	}





	if(isset($_POST["timezone"])){
		$timezone = escapeshellarg($_POST["timezone"]);

		$valid_timezones = array("Pacific/Midway","America/Adak","Etc/GMT+10","Pacific/Marquesas","Pacific/Gambier","America/Anchorage","America/Ensenada","Etc/GMT+8","America/Los_Angeles","America/Denver","America/Chihuahua","America/Dawson_Creek","America/Belize","America/Cancun","Chile/EasterIsland","America/Chicago","America/New_York","America/Havana","America/Bogota","America/Caracas","America/Santiago","America/La_Paz","Atlantic/Stanley","America/Campo_Grande","America/Goose_Bay","America/Glace_Bay","America/St_Johns","America/Araguaina","America/Montevideo","America/Miquelon","America/Godthab","America/Argentina/Buenos_Aires","America/Sao_Paulo","America/Noronha","Atlantic/Cape_Verde","Atlantic/Azores","Europe/Belfast","Europe/Dublin","Europe/Lisbon","Europe/London","Africa/Abidjan","Europe/Amsterdam","Europe/Belgrade","Europe/Brussels","Africa/Algiers","Africa/Windhoek","Asia/Beirut","Africa/Cairo","Asia/Gaza","Africa/Blantyre","Asia/Jerusalem","Europe/Minsk","Asia/Damascus","Europe/Moscow","Africa/Addis_Ababa","Asia/Tehran","Asia/Dubai","Asia/Yerevan","Asia/Kabul","Asia/Yekaterinburg","Asia/Tashkent","Asia/Kolkata","Asia/Katmandu","Asia/Dhaka","Asia/Novosibirsk","Asia/Rangoon","Asia/Bangkok","Asia/Krasnoyarsk","Asia/Hong_Kong","Asia/Irkutsk","Australia/Perth","Australia/Eucla","Asia/Tokyo","Asia/Seoul","Asia/Yakutsk","Australia/Adelaide","Australia/Darwin","Australia/Brisbane","Australia/Hobart","Asia/Vladivostok","Australia/Lord_Howe","Etc/GMT-11","Asia/Magadan","Pacific/Norfolk","Asia/Anadyr","Pacific/Auckland","Etc/GMT-12","Pacific/Chatham","Pacific/Tongatapu","Pacific/Kiritimati");

		if(in_array($timezone,$valid_timezones)){

			$mysqli = db_connect();

			$stmt1 = $mysqli->prepare("Update Users Set Timezone = ?");

			$stmt1->bind_param("s", $timezone);

			$stmt1->execute();

			$stmt1->close();
			
			exec("sudo timedatectl set-timezone $timezone");


		}
		else{
			echo "wrong timezone";
			exit();
		}



	if(isset($_POST["ntpserver"])){               
 		$pntpserver = preg_replace("/[^0-9A-Za-z\. ]/","",$_POST["ntpserver"]);

                exec("sudo /bin/cat /etc/systemd/timesyncd.conf| grep -w NTP|awk -F \"=\" '{print$2}'",$outputntp,$result);

                $entpserver = $outputntp[0];

                if(!empty($pntpserver)){

                $str=file_get_contents('/etc/systemd/timesyncd.conf');
                $str=str_replace($entpserver,$pntpserver,$str);
                file_put_contents('/etc/systemd/timesyncd.conf', $str);
		exec("sudo service systemd-timesyncd restart",$outputntp1,$result1);

                header('location:deviceSettings.php?dnsupdate=success');
                exit();
                }
		
	}

header('location:deviceSettings.php?dnsupdate=success');
exit();
}




	if(isset($_POST["pdnsserver"]) && $_SESSION['csrf_token'] == $_POST['csrf_token']){               

		exec("sudo /bin/cat /etc/network/interfaces|grep \"dns-nameservers\" |awk -F \"dns-nameservers\" '{print$2}'|xargs",$outputdns,$result);
		
		$ednsserver = $outputdns[0];
 		$pdnsserver = preg_replace("/[^0-9\.]/","",$_POST["pdnsserver"]);

                if(!empty($pdnsserver)){

                $str=file_get_contents('/etc/network/interfaces');
                $str=str_replace($ednsserver,$pdnsserver,$str);
                file_put_contents('/etc/network/interfaces', $str);

		exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
		exec("sudo shutdown -r now");

		header('location:deviceSettings.php?dnsupdate=success');
		exit();
		}

       }
}


function devicereset()
{
        exec("sudo /etc/devicereset.sh");
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

	exec("sudo /bin/rm -r /var/dejavufiles/backup*");

	exec("sudo /bin/mkdir /var/dejavufiles/backup");

	exec("sudo /bin/chmod 777 /var/dejavufiles/backup");

	exec("sudo /bin/cp -f /etc/network/interfaces /var/dejavufiles/backup/interfaces");
	exec("sudo /bin/cp -f /etc/apache2/ports.conf /var/dejavufiles/backup/ports.conf");
	exec("sudo /bin/cp -f /etc/ssh/sshd_config /var/dejavufiles/backup/sshd_config");
	#exec("sudo /bin/cp -f /etc/rsyslog.d/magento-example.com.conf /var/dejavufiles/backup/magento-example.com.conf");
	exec("sudo /bin/cp -f /etc/systemd/timesyncd.conf /var/dejavufiles/backup/timesyncd.conf");
	exec("sudo /bin/cp -f /etc/resolv.conf /var/dejavufiles/backup/resolv.conf");

	#exec("sudo /bin/cp -r /var/dejavufiles/uploads /var/dejavufiles/backup");
	//backuping up current database

	exec("sudo mysqldump -u root VyuhaPro > /var/dejavufiles/backup/db.sql");

	//zip the whole backup folder
	exec("cd /var/dejavufiles && sudo zip -r backup.zip backup");


	$file = '/var/dejavufiles/backup.zip';

	if (file_exists($file)) {
    		header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
    		header('Content-Disposition: attachment; filename='.basename($file));
    		header('Content-Transfer-Encoding: binary');
    		header('Expires: 0');
    		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    		header('Pragma: public');
    		header('Content-Length: ' . filesize($file));
    		ob_clean();
    		flush();
    		readfile($file);
    		exit;
	}


	#exec("sudo /bin/mv -f /var/dejavufiles/backup.zip /var/www/html/VyuhaPro/backup.zip");	

	//exec("sudo /bin/rm -r /var/dejavufiles/backup");

	#header('location:backup.zip'); 
	
	exit();

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
			header('location:deviceSettings.php?pass=fail');
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

				header('location:deviceSettings.php?pass=success');
				exit();
	          
			}
			else
			{
				header('location:deviceSettings.php?pass=fail');
				exit();
			}

		}
}

function updateInterface($ipad, $mask, $gateway){

	exec("sudo /bin/sh /var/log/data/setup.sh $ipad $mask $gateway");	
	exit();
}

function updateSMTP($smtp_hostname, $smtp_username, $smtp_password, $PortNumber, $From_Email){


	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT id FROM SMTPDetails;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
	
		$stmt->close();

		//Insert if no details

		$mysqli = db_connect();

		//add default alert
		$stmt2 = $mysqli->prepare("Insert Into SMTPDetails (Hostname, Username, Password, PortNumber, Status, From_Email) VALUES (?,?,?,?,?,?)");

		$status = 1;

		$stmt2->bind_param("ssssss", $smtp_hostname, $smtp_username, $smtp_password,$PortNumber,$status,$From_Email);

		$stmt2->execute();

		$stmt2->close();

		header('location:deviceSettings.php?smtp=success');
		
		exit();

	}

	else{
		//Update if details present

		$mysqli = db_connect();

		//add default alert
		$stmt2 = $mysqli->prepare("Update SMTPDetails Set Hostname = ? , Username = ?, Password = ?, PortNumber =?, From_Email=? Where Status = 1");

		$status = 1;

		$stmt2->bind_param("sssss", $smtp_hostname, $smtp_username, $smtp_password, $PortNumber,$From_Email);

		$stmt2->execute();

		$stmt2->close();

		header('location:deviceSettings.php?smtp=success');

		exit();
	}

}

function testEmail($test_emailaddress)
{
	
	//Get SMTP details

	$smtp = get_SMTPDetials();

	$hostname = $smtp['Hostname'];
	$username = $smtp['Username'];
	$password = $smtp['Password'];
	$PortNumber = $smtp['PortNumber'];
	$From_Email = $smtp['From_Email'];

	if($PortNumber == '')
	{
		$PortNumber = '25';
	}

	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	$mail->isSMTP();
	//Enable SMTP debugging
	$mail->SMTPDebug = 1;
	if(!empty($username) && !empty($password)) {
		$mail->SMTPAuth = true;
		$mail->Username = $username;
		$mail->Password = $password;
	}	
	else {
		$mail->SMTPAuth = false;
		$mail->SMTPAutoTLS = false;
		$mail->SMTPSecure = false;
	}
	
	//$mail->SMTPSecure =PHPMailer::ENCRYPTION_SMTPS;
	//$mail->SMTPAutoTLS = false;
	//$mail->SMTPSecure = false;
	$mail->Host = $hostname;
	$mail->Port = $PortNumber; //Defaults to 443
	$mail->IsHTML(true);
	$mail->SetFrom($From_Email);
	$mail->addReplyTo($From_Email);
	$mail->addAddress($test_emailaddress);

	$mail->Subject = 'SMTP Test Email - Dejavu';
	
	$message = 'This is a Test Email for checking SMTP Details from Dejavu';

	$mail->msgHTML($message);


	if (!$mail->send()) {
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		$errorInfo = $mail->ErrorInfo;

		header("location:deviceSettings.php?smtp_test=".$errorInfo);

	} else {
		header('location:deviceSettings.php?smtp_test=Message_Sent');
	}
}

function reboot()
{
	exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
	exec("sudo shutdown -r now");	
	exit();
}

function shutdown()
{
	exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
        exec("sudo init 0");
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
		
		#exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/magento-example.com.conf /etc/rsyslog.d/magento-example.com.conf");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/timesyncd.conf /etc/systemd/timesyncd.conf");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/resolv.conf /etc/resolv.conf");
	
		//restoring db

		exec("sudo mysql -u root VyuhaPro < /var/dejavufiles/configbackup/backup/db.sql");
		
		exec("sudo rm -r /var/dejavufiles/configbackup/*");		
		
		$location = "location:backupSettings.php?config=success&interface=". $interface; 

		header($location);
		
		exit();
	}
}

?>