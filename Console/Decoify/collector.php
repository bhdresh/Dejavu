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

function saveLog($user_id, $auth_key, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $Timestamp, $rawlog, $serviceType, $pcap_filename, $video_filename)
{
	$mysqli = db_connect();

	$status = 1;

	$logsavedtime = $Timestamp;

	$stmt = $mysqli->prepare("Insert Into CloudLogs (user_id, auth_key, Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, Service_Name,EventType, pcap_filename, video_filename, Raw_Logs, TimeStamp) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssssssssss", $user_id, $auth_key, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $serviceType, $eventType, $pcap_filename, $video_filename, $rawlog, $logsavedtime);

	$stmt->execute();

	$stmt->close();

}

function saveEvent($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp)
{
	$alertPresent = checkAlert($decoyIP, $attackerIP, $user_id);

	$currentTime = $timestamp;

	if($alertPresent == 0)
	{

		$mysqli = db_connect();

		$status = 1;

		$stmt = $mysqli->prepare("Insert Into Alerts (Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp, Status, user_id) VALUES (?,?,?,?,?,?,?)");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("sssssis", $decoyName, $decoyGroup, $decoyIP, $attackerIP, $currentTime, $status, $user_id);

		$stmt->execute();

		$stmt->close();

	}
	else{
		$mysqli = db_connect();

		$status = 1;

		$stmt = $mysqli->prepare("Update Alerts SET LogInsertedTimeStamp=? Where Decoy_IP=? and Attacker_IP=? and user_id=?");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("ssss", $currentTime, $decoyIP, $attackerIP, $user_id);

		$stmt->execute();

		$stmt->close();
	}

}

function checkAlert($decoyIP, $attackerIP, $user_id)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT LogInsertedTimeStamp FROM Alerts where Decoy_IP= ? And Attacker_IP = ? And Status = 1 And user_id = ? ");

	$stmt->bind_param("sss",$decoyIP, $attackerIP, $user_id);

	$stmt->execute();

	$result = $stmt->get_result();

	if($result->num_rows === 0){
		$msg = 0;
	}

	else{
		$msg = 1;
	}

	$stmt->close();

	return $msg;
}

function getLatestLogtime($decoyIP, $attackerIP)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT LogInsertedTimeStamp FROM Alerts where Decoy_IP= ? And Attacker_IP = ? ORDER BY LogInsertedTimeStamp DESC LIMIT 1");

	$stmt->bind_param("ss",$decoyIP, $attackerIP);

	$stmt->execute();

	$result = $stmt->get_result();

	if($result->num_rows === 0){
		$msg = 'No Log';
	}

	else{
		
		while($row = $result->fetch_assoc()) {
			$event[] = $row;
		}

		$msg = $event[0]['LogInsertedTimeStamp'];
	}

	$stmt->close();

	return $msg;
}

function getFilter($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT Alert_Info, Email_To FROM AlertConfig where status = 1 and User_ID=?");

	$stmt->bind_param("s", $user_id);

	$stmt->execute();

	$result = $stmt->get_result();

	if($result->num_rows === 0){
	
		//saveEvent($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp);
	}

	else{
		$event = [];

		while($row = $result->fetch_array()) {

			array_push($event, $row);
		
		}

		return $event;

		$stmt->close();

	}

}

function updateMailCount($user_id, $timestamp,$count){

	$mysqli = db_connect();

	if($count == 0)
	{
		$stmt = $mysqli->prepare("UPDATE Users SET mail_count = 0, mail_last_sent= ? WHERE user_id = ?");
	}

	if($count == 1)
	{
		$stmt = $mysqli->prepare("UPDATE Users SET mail_count = IFNULL(mail_count, 0) + 1, mail_last_sent= ? WHERE user_id = ?");
	}

	$stmt->bind_param("ss", $timestamp, $user_id);

	$stmt->execute();

	return 0;

}

function getMailCount($user_id){

	$mysqli = db_connect();

	$stmt = $mysqli->prepare("Select mail_count, DATE(mail_last_sent) = DATE(NOW()) as sent_today from Users WHERE user_id = ?;");

	$stmt->bind_param("s", $user_id);

	$stmt->execute();

	$result = $stmt->get_result();

	$event = [];

	while($row = $result->fetch_assoc()) {
		array_push($event, $row);
	}

	return $event;

	$stmt->close();

}

function AlertCompare($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType)
{
	$filter_criteria = getFilter($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp);

	foreach ($filter_criteria as $key => $value) {

		$email_alert = $filter_criteria[$key]['Email_To'];

		//echo $email_alert;
			
		$filter = json_decode(stripslashes($filter_criteria[$key]['Alert_Info']), true);
		
		//print_r($filter_criteria[$key]['Alert_Info']);

		$email_send = [];

		$filter_type = $filter[0]['filter'];

		foreach ($filter[0]['alertCriteria'] as $value) {
			
			$case = '';

			$criteria = $value['criteria'];

			$condition = $value['condition'];

			$search_data = $value['search_data'];
			/*
			$decoyName = $alertData->decoyName;
			$decoyGroup = $alertData->decoyGroup;
			$serviceType = $alertData->serviceType;
			$eventType = $alertData->eventType;
			$decoyIP = $alertData->decoyIP;
			$attackerIP = $alertData->attackerIP;
			*/

			$search_data_ar = explode(',', $search_data);

			foreach ($search_data_ar as $search_data_key => $search_data_val) {
				
				if($condition == 'eq'){
					if(trim($$criteria) == trim($search_data_val)){
						$case = 'pass';
					}
				}
					
				elseif($condition == 'not_eq'){
					if(trim($$criteria) != trim($search_data_val)){
						$case = 'pass';
					}
				}
			}

			if($case == 'pass')
			{
				array_push($email_send, 'p');
			}
			else
			{
				array_push($email_send, 'f');
			}
		}

		if($filter_type == 'all'){
			//Check for any false condition
			if (!in_array("f", $email_send)){
				$alertPresent = checkAlert($decoyIP, $attackerIP, $user_id);
				saveEvent($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp);
				//Send Email out if event not present
				if($alertPresent == 0)
				{
					//Check if today mail count is greater then 5

					$data = getMailCount($user_id);

					$mail_count = $data[0]['mail_count'];

					$sent_today = $data[0]['sent_today'];

					if ($sent_today == 1 && $mail_count > 5)
					{
						//don't send email
					}
					if ($sent_today == 0)
					{
						$count = 0;
						updateMailCount($user_id, $timestamp,$count);
						$msg_status = 0;
						alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert, $msg_status);
					}
					if ($sent_today == 1 && $mail_count <= 5)
					{
						$count = 1;//increment count
						updateMailCount($user_id, $timestamp,$count);
						$msg_status = 0;
						if($mail_count == 5)
						{
							$msg_status = 1;
						}
						alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert, $msg_status);
					}
					
					
					
				}
			}
		}
		elseif($filter_type == 'any'){
			//Even if one statement true
			if(in_array("p", $email_send)){
				$alertPresent = checkAlert($decoyIP, $attackerIP, $user_id);
				saveEvent($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp);
				//Send Email out if event not present
				if($alertPresent == 0)
				{
					
					//Check if today mail count is greater then 5

					$data = getMailCount($user_id);

					$mail_count = $data[0]['mail_count'];

					$sent_today = $data[0]['sent_today'];

					if ($sent_today == 1 && $mail_count > 5)
					{
						//don't send email
					}
					if ($sent_today == 0)
					{
						$count = 0;
						updateMailCount($user_id, $timestamp,$count);
						alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert, $msg);
					}
					if ($sent_today == 1 && $mail_count < 5)
					{
						$count = 1;//increment count
						updateMailCount($user_id, $timestamp,$count);
						alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert, $msg);
					}

					
				}
			}
		}

		

		//print_r($email_send);

		/* Logic to Compare
		$criteria = $criteria;
		$decoyGroup = "HTTP_Dec_IT1";//from Log
		$condition = $condition;
		$data = $search_value;
		
		$testConditions = $$criteria . $condition . $data;
		
		echo $testConditions;
		if($testConditions) echo "Condition Matched";
		*/
	}

	saveEvent($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $timestamp);

}

function alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert, $msg_status)
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
		
		//$mail->SMTPSecure = 'ssl';
		
		$mail->Host = $hostname;
		$mail->Port = $PortNumber; //Defaults to 443
		$mail->IsHTML(true);
		//$mail->Username = $username;
		//$mail->Password = $password;
		$mail->SetFrom($From_Email);
		$mail->addReplyTo($From_Email);
		$mail->addAddress($email_alert);

		$mail->Subject = 'Intrusion Alert';

		//
		$message = file_get_contents('alert_template.html'); 
		$message = str_replace('%decoyName%', $decoyName, $message); 
		$message = str_replace('%decoyIP%', $decoyIP, $message);
		$message = str_replace('%attackerIP%', $attackerIP, $message); 
		$message = str_replace('%Time%', $timestamp, $message);
		if($msg_status == 1)
		{
			$limit = "You have reached maximum emails you can recieve for a day. Please check DejaVu Dashboard for all the events";
			$message = str_replace('%limit%', $limit, $message);
		}

		$mail->msgHTML($message);

		if (!$mail->send()) {
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
		    echo 'Message sent!';
		}
	
}


if (isset($_POST['auth_key']))
{


$remote_ip=$_SERVER["REMOTE_ADDR"];
$auth_key=preg_replace("/[^0-9a-zA-Z]/","",$_POST["auth_key"]);
$decoyName=preg_replace("/[^0-9a-zA-Z\-\_]/","",$_POST["Decoy_Name"]);
$decoyGroup=preg_replace("/[^0-9a-zA-Z\-\_]/","",$_POST["Decoy_Group"]);
$decoyIP=preg_replace("/[^0-9\.]/","",$_POST["Decoy_IP"]);
$attackerIP=preg_replace("/[^0-9\.]/","",$_POST["Attacker_IP"]);
$eventType=preg_replace("/[^0-9a-zA-Z\-\_\.\": <>=\[\]]/","",$_POST["EventType"]);	
$rawlog=preg_replace("/[^0-9a-zA-Z\-\_\.\": <>=\[\]]/","",$_POST["Raw_Logs"]);
$serviceType=preg_replace("/[^0-9a-zA-Z\-]/","",$_POST["Service_Name"]);
$timestamp = preg_replace("/[^0-9\-: ]/","",$_POST["Timestamp"]);
$pcap_filename = preg_replace("/[^0-9a-zA-Z\.]/","",$_POST["pcap_filename"]);
$video_filename = preg_replace("/[^0-9a-zA-Z\.]/","",$_POST["video_filename"]);

if(!empty($auth_key))
{

	$mysqli = db_connect();
	$stmt = $mysqli->prepare("SELECT * FROM Users WHERE auth_key=?");
	$stmt->bind_param("s", $auth_key);
	$stmt->execute();
	$result = $stmt->get_result();
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			$user_id=$row['user_id'];
		}
	} else {
		echo "Invalid Auth Key";
		exit();
	}

	saveLog($user_id, $auth_key, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $pcap_filename, $video_filename);
	AlertCompare($user_id, $decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType);

	} 
	else {
			echo "FALSE";	
	}

}


?>
