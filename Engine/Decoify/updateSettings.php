<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

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

	if(isset($_GET['check']))
	{
		session_write_close();
		if(check_alive() == 'Valid_Key')
		{
			if(GetApiStatus() != 1){
				echo "API connection sucessful, however logs are not being currently forwarded to DejaVu Console. Enable 'Send Logs to MiragEye Console' below on Syslog Details section";
			}
			else{
				echo "API Connection Sucessful";
			}
			
		}
		elseif(check_alive() == 'Connection Ok! But Invalid API Key!')
		{
			echo "<p class=\"text-red\">Connection to DejaVu seems to be okay. However, the API key is not a valid one. Update your API key below and try again!</p>";
		}
		else
		{
			echo "<p class=\"text-red\">API Connection Failed! Check API KEY and ensure connection to DejaVu Console!</p>";
		}
		exit();
	}

	if(isset($_POST["secret-key"])){
		$secretkey = $_POST["secret-key"];

		updateApiKey($secretkey);
	}

	if(isset($_POST["Console_IP"])){
		$Console_IP = $_POST["Console_IP"];

		updateConsoleIP($Console_IP);
	}



	

	if (isset($_POST['dockerip']) && isset($_POST['dockermask'])){

		if(val_ip($_POST['dockerip']) && val_ip($_POST["dockermask"])){
		
		$newdockerip=$_POST['dockerip'];
		$newdockermask=$_POST['dockermask'];

                exec("sudo /usr/bin/sipcalc docker0|grep -i \"Network mask\"|grep -i \"bits\" |head -1|awk -F \"- \" '{print$2}'|xargs",$outputcidrmask,$result);
                exec("sudo /usr/bin/sipcalc docker0|grep -i \"Host address\"| head -1|awk -F \"- \" '{print$2}'",$outputnetworkadd,$result);

                $cidrmask=$outputcidrmask[0];
                $networkadd=$outputnetworkadd[0];

		exec("sudo /usr/bin/sipcalc $newdockerip $newdockermask|grep -i \"Network mask\"|grep -i \"bits\" |head -1|awk -F \"- \" '{print$2}'|xargs",$outputcidrmasknew,$result);
                exec("sudo /usr/bin/sipcalc $newdockerip $newdockermask|grep -i \"Host address\"| head -1|awk -F \"- \" '{print$2}'",$outputnetworkaddnew,$result);

                $cidrmasknew=$outputcidrmasknew[0];
                $networkaddnew=$outputnetworkaddnew[0];

		$oldstring = $networkadd."/".$cidrmask;
		$newstring = $networkaddnew."/".$cidrmasknew;
		$str=file_get_contents('/lib/systemd/system/docker.service');
		$str=str_replace($oldstring,$newstring,$str);
		file_put_contents('/lib/systemd/system/docker.service', $str);

		exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
		exec('sudo kill -9 `sudo ps auxx| grep -i snort| grep -i fast|awk -F " " \'{print$2}\'|xargs`');
		exec("sudo reboot");
		
		header('location:deviceSettings.php?dnsupdate=success');

		exit();
		}

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
		exec('sudo kill -9 `sudo ps auxx| grep -i snort| grep -i fast|awk -F " " \'{print$2}\'|xargs`');
		exec("sudo shutdown -r now");

		header('location:deviceSettings.php?dnsupdate=success');
		exit();
		}

       }





	if(isset($_POST["timezone"])){
		
		$timezone = preg_replace("/[^0-9a-zA-Z\-\_\.\/\+]/","",$_POST["timezone"]);

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

		//to enable/disable API

		if(isset($_POST["EnableApi"]) && $_POST["EnableApi"] == 'yes')
		{
			
			//Update if details present

			$mysqli = db_connect();

			//add default alert
			$stmt1 = $mysqli->prepare("Update Users Set EnableApi = ?");

			$EnableApi = 1;

			$stmt1->bind_param("i", $EnableApi);

			$stmt1->execute();

			$stmt1->close();
		}

		else
		{
			//Update if details not present

			$mysqli = db_connect();

			//add default alert
			$stmt1 = $mysqli->prepare("Update Users Set EnableApi = ?");

			$DisableApi = 0;

			$stmt1->bind_param("i", $DisableApi);

			$stmt1->execute();

			$stmt1->close();

		}
		

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

		header('location:cloudSettings.php?smtp=success');
        exit();	
	}



	if(isset($_POST["backup"]) && isset($_POST["backup"]) == '1')
	{
		downloadFile();
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

	header('location:backupSettings.php');
	exit();

}

else{
	header('location:backupSettings.php');
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
	exec("sudo /bin/cp -f /etc/rsyslog.d/magento-example.com.conf /var/dejavufiles/backup/magento-example.com.conf");
	exec("sudo /bin/cp -f /etc/systemd/timesyncd.conf /var/dejavufiles/backup/timesyncd.conf");
	exec("sudo /bin/cp -f /etc/resolv.conf /var/dejavufiles/backup/resolv.conf");

	exec("sudo /bin/cp -r /var/dejavufiles/uploads /var/dejavufiles/backup");
	//backuping up current database

	exec("sudo mysqldump -u root dejavu > /var/dejavufiles/backup/db.sql");

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


	#exec("sudo /bin/mv -f /var/dejavufiles/backup.zip /var/www/html/Decoify/backup.zip");

	//exec("sudo /bin/rm -r /var/dejavufiles/backup");

	#header('location:backup.zip'); 
	
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
		
		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/uploads/*.zip /var/dejavufiles/uploads");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/magento-example.com.conf /etc/rsyslog.d/magento-example.com.conf");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/timesyncd.conf /etc/systemd/timesyncd.conf");

		exec("sudo /bin/mv -f /var/dejavufiles/configbackup/backup/resolv.conf /etc/resolv.conf");
	
		//restoring db

		exec("sudo mysql -u root dejavu < /var/dejavufiles/configbackup/backup/db.sql");
		
		exec("sudo rm -r /var/dejavufiles/configbackup/*");		
		
		$location = "location:backupSettings.php?config=success&interface=". $interface; 

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
	exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
	exec('sudo kill -9 `sudo ps auxx| grep -i snort| grep -i fast|awk -F " " \'{print$2}\'|xargs`');
	exec("sudo shutdown -r now");	
	exit();
}

function shutdown()
{
	exec('sudo kill -9 `sudo service rc-local status |egrep -o "─[0-9]+ "|egrep -o "[0-9]+"|xargs`');
	exec('sudo kill -9 `sudo ps auxx| grep -i snort| grep -i fast|awk -F " " \'{print$2}\'|xargs`');
        exec("sudo init 0");
        exit();
}

function devicereset()
{
        exec("sudo /etc/devicereset.sh");
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

		header('location:backupSettings.php?smtp=success');
		
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

		header('location:backupSettings.php?smtp=success');

		exit();
	}

}

function updateApiKey($secretkey)
{
	$username = $_SESSION['user_name'];

	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Update Users SET ApiKey = ? where Username= ?");

	if (!$stmt) {
		throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("ss", $secretkey, $username);

	$stmt->execute();

	$stmt->close();

	header('location:cloudSettings.php?key=success');

	exit();

}

function updateConsoleIP($Console_IP)
{
	$username = $_SESSION['user_name'];

	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Update Users SET Console_IP = ? where Username= ?");

	if (!$stmt) {
		throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	$stmt->bind_param("ss", $Console_IP, $username);

	$stmt->execute();

	$stmt->close();

	header('location:cloudSettings.php?key=IPsuccess');

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

?>
