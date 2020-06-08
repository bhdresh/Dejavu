<?php

include 'db.php';
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function saveLog($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType)
{
	$mysqli = db_connect();

	$status = 1;

	$logsavedtime = date("Y-m-d H:i:s");

	$stmt = $mysqli->prepare("Insert Into MasterLogs (Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, Service_Name,EventType, Raw_Logs, LogInsertedTimeStamp) VALUES (?,?,?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssssss", $decoyName, $decoyGroup, $decoyIP, $attackerIP, $serviceType, $eventType, $rawlog,$logsavedtime);

	$stmt->execute();

	$stmt->close();

}

/*
function saveEvent($decoyName, $decoyGroup, $decoyIP, $attackerIP)
{
	echo "Enter saveEvent block  ";

	$lastlogTime = getLatestLogtime($decoyIP, $attackerIP);

	echo " lastlogTime ".$lastlogTime;

	$currentTime = date("Y-m-d H:i:s");

	$nInterval = strtotime($currentTime) - strtotime($lastlogTime); 

	$nInterval = round($nInterval/60);

	echo "  nInterval ".$nInterval;

	if($nInterval > 5 || $lastlogTime == 'No Log')
	{

		$mysqli = db_connect();

		$status = 1;

		$stmt = $mysqli->prepare("Insert Into Alerts (Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp, Status) VALUES (?,?,?,?,?,?)");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("sssssi", $decoyName, $decoyGroup, $decoyIP, $attackerIP, $currentTime, $status);

		$stmt->execute();

		$stmt->close();

	}

}
*/

function saveEvent($decoyName, $decoyGroup, $decoyIP, $attackerIP)
{
	$alertPresent = checkAlert($decoyIP, $attackerIP);

	$currentTime = date("Y-m-d H:i:s");

	if($alertPresent == 0)
	{

		$mysqli = db_connect();

		$status = 1;

		$stmt = $mysqli->prepare("Insert Into Alerts (Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, LogInsertedTimeStamp, Status) VALUES (?,?,?,?,?,?)");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("sssssi", $decoyName, $decoyGroup, $decoyIP, $attackerIP, $currentTime, $status);

		$stmt->execute();

		$stmt->close();

	}
	else{
		$mysqli = db_connect();

		$status = 1;

		$stmt = $mysqli->prepare("Update Alerts SET LogInsertedTimeStamp=? Where Decoy_IP=? and Attacker_IP=?");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("sss", $currentTime, $decoyIP, $attackerIP);

		$stmt->execute();

		$stmt->close();
	}

}

function checkAlert($decoyIP, $attackerIP)
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT LogInsertedTimeStamp FROM Alerts where Decoy_IP= ? And Attacker_IP = ? And Status = 1");

	$stmt->bind_param("ss",$decoyIP, $attackerIP);

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

function getFilter()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT Alert_Info, Email_To FROM AlertConfig where status = 1;");

	$stmt->execute();

	$result = $stmt->get_result();

	if($result->num_rows === 0) exit('No rows');

	$event = [];

	while($row = $result->fetch_array()) {

	array_push($event, $row);
	
	}

	return $event;

	$stmt->close();
}

function AlertCompare($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType)
{
	/*
	//Data from Logs
	$alert = '{
	  "attackerIP": "10.23.24.23",
	  "decoyName": "HTTP_Decoy1",
	  "decoyIP": "192.168.12.23",
	  "decoyGroup": "IT_Vlan1",
	  "eventType": "HTTP Login",
	  "serviceType": "HTTP",
	  "time":"2018-04-01 12:14:57",
	  "raw log": "HTTP login Attempt to 192.168.12.23 with username:admin, password:P@ss"
	}';
	*/

	//$alertData = json_decode($alert);

	$filter_criteria = getFilter();

	print_r($filter_criteria);

	foreach ($filter_criteria as $key => $value) {

		$email_alert = $filter_criteria[$key]['Email_To'];

		echo $email_alert;
			
		$filter = json_decode(stripslashes($filter_criteria[$key]['Alert_Info']), true);
		
		print_r($filter_criteria[$key]['Alert_Info']);

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
				saveEvent($decoyName, $decoyGroup, $decoyIP, $attackerIP);
				alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert);
			}

		}
		if($filter_type == 'any'){
			//Even if one statement true
			if(in_array("p", $email_send)){
				saveEvent($decoyName, $decoyGroup, $decoyIP, $attackerIP);
				alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert);
			}

		}
		if($filter_type == 'none'){
			saveEvent($decoyName, $decoyGroup, $decoyIP, $attackerIP);
			alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert);

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

		echo "<br>";
		echo "<br>";
		echo "<br>";
	}

}

function alertEmail($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType, $email_alert)
{

	//Get SMTP Details 

	$mysqli = db_connect();
  
  	$stmt = $mysqli->prepare("SELECT Hostname, Username, Password FROM SMTPDetails");

  	$stmt->execute();

 	$result = $stmt->get_result();

  	//$arr = $result->fetch_assoc();

  	$row = $result->fetch_array();

  	$stmt->close();

  	//---------

  	$hostname = $row['Hostname'];
  	$username = $row['Username'];
  	$password = $row['Password'];

  	if($hostname && $username && $password)
  	{
  		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		$mail->isSMTP();
		//Enable SMTP debugging
		$mail->SMTPDebug = 2;
		$mail->Host = $hostname;
		$mail->Port = 25;
		$mail->SMTPOptions = array(
		                    'ssl' => array(
		                        'verify_peer' => false,
		                        'verify_peer_name' => false,
		                        'allow_self_signed' => true
		                    )
		                );
		$mail->SMTPAuth = true;

		$mail->Username = $username;

		$mail->Password = $password;

		$mail->setFrom('alert@dejavu.intra', 'From Dejavu Alert');

		$mail->addReplyTo('alert@dejavu.intra', 'From Dejavu Alert');

		$mail->addAddress($email_alert, $email_alert);

		$mail->Subject = 'PHPMailer SMTP test';

		//
		$message = file_get_contents('alert_template.html'); 
		$message = str_replace('%decoyName%', $decoyName, $message); 
		$message = str_replace('%decoyIP%', $decoyIP, $message);
		$message = str_replace('%attackerIP%', $attackerIP, $message); 
		$message = str_replace('%eventType%', $eventType, $message);
		$message = str_replace('%rawLog%', $rawlog, $message);
		$message = str_replace('%serviceType%', $serviceType, $message);
		$message = str_replace('%Time%', $timestamp, $message);

		$mail->msgHTML($message);

		$mail->AltBody = 'This is a plain-text message body';

		if (!$mail->send()) {
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
		    echo 'Message sent!';
		}
  	}
	
}

	$val = getopt(null, ["decoyName:", "decoyGroup:", "decoyIP:", "attackerIP:", "eventType:", "timestamp:", "rawlog:", "serviceType:"]);	
	$decoyName = $val['decoyName'];
	$decoyGroup = $val['decoyGroup'];
	$decoyIP = $val['decoyIP'];
	$attackerIP = $val['attackerIP'];
	$eventType = $val['eventType'];
	$timestamp = $val['timestamp'];
	$rawlog = $val['rawlog'];
	$serviceType = $val['serviceType'];

	saveLog($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType);


	AlertCompare($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $timestamp, $rawlog, $serviceType);



?>
