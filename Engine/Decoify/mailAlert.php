<?php

include 'db.php';

//Get API key

$apikey = GetApiKey();

//Get API status

$apiStatus = GetApiStatus();
$SyslogStatus = SyslogStatus();
//URL to push logs to

$Console_IP = GetConsole_IP();

$url = 'https://'.$Console_IP.'/Decoify/collector.php';

$uploadurl = 'https://'.$Console_IP.'/Decoify/fileupload.php';

#$uploadurl = 'https://mirageye.camolabs.io/Decoify/fileupload.php';

$logtime = date("Y-m-d H:i:s");

function saveLog($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $eventCategory, $timestamp, $rawlog, $serviceType, $decoy_type, $pcap_filename, $video_filename, $msg_filename)
{
	$mysqli = db_connect();

	$status = 1;

	$stmt = $mysqli->prepare("Insert Into MasterLogs (Decoy_Name, Decoy_Group, Decoy_IP, Attacker_IP, Service_Name,EventType, EventCategory, pcap_filename, video_filename, msg_filename, Raw_Logs, LogInsertedTimeStamp, decoy_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("ssssssssssssi", $decoyName, $decoyGroup, $decoyIP, $attackerIP, $serviceType, $eventType, $eventCategory, $pcap_filename, $video_filename, $msg_filename, $rawlog,$logtime,$decoy_type);

	$stmt->execute();

	$stmt->close();

}

// Get all the values

$val = getopt(null, ["decoyName:", "decoyGroup:", "decoyIP:", "attackerIP:", "eventType:", "eventCategory:","timestamp:", "rawlog:", "serviceType:", "decoyType:", "pcap_filename:", "video_filename:", "msg_filename:"]);

$decoyName = $val['decoyName'];
$decoyGroup = $val['decoyGroup'];
$decoyIP = $val['decoyIP'];
$attackerIP = $val['attackerIP'];
$eventType = $val['eventType'];
$eventCategory = $val['eventCategory'];
$timestamp = $val['timestamp'];
$rawlog = $val['rawlog'];
$serviceType = $val['serviceType'];
$decoy_type = $val['decoyType'];
$pcap_filename = $val['pcap_filename'];
$video_filename= $val['video_filename'];
$msg_filename= $val['msg_filename'];

if($apiStatus == 1)
{

	//check for connectivity with server


	if(check_alive() == 'Valid_Key')
	{	
		//Send current log


		$data = array('auth_key' => $apikey, 'Decoy_Name' => $decoyName, 'Decoy_Group' => $decoyGroup, 'Decoy_IP' => $decoyIP,'Attacker_IP' => $attackerIP, 'EventType' => $eventType, 'Raw_Logs' => $rawlog, 'Service_Name' => $serviceType, 'Timestamp' => $logtime, 'decoy_type' => $decoy_type, 'pcap_filename' => $pcap_filename, 'video_filename' => $video_filename, 'msg_filename' => $msg_filename);

		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		'method'  => 'POST',
				'content' => http_build_query($data)
		),
		"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false)
		);

		$context  = stream_context_create($options);
		//echo $context;
		$result = file_get_contents($url, false, $context);

		if ($result === FALSE) { /* Handle error */ }



		//Upload pcap file

		if (!empty($pcap_filename))
		{

		define('MULTIPART_BOUNDARY', '--------------------------'.microtime(true));
		$header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
		define('FORM_FIELD', 'uploaded_file'); 
		$filename = "/var/dejavufiles/captures/$pcap_filename";
		$file_contents = file_get_contents($filename);    

		$content =  "--".MULTIPART_BOUNDARY."\r\n".
            	"Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
            	"Content-Type: application/octet-stream\r\n\r\n".
            	$file_contents."\r\n";

		// add some POST fields to the request too: $_POST['foo'] = 'bar'
		$content .= "--".MULTIPART_BOUNDARY."\r\n".
            	"Content-Disposition: form-data; name=\"auth_key\"\r\n\r\n".
            	"$apikey\r\n";

		// signal end of request (note the trailing "--")
		$content .= "--".MULTIPART_BOUNDARY."--\r\n";
		$context = stream_context_create(array(
    			'http' => array(
          		'method' => 'POST',
          		'header' => $header,
          		'content' => $content,
    			),
			"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false)
		));
		$result = file_get_contents($uploadurl, false, $context);
		unlink("/var/dejavufiles/captures/$pcap_filename");

		}

		//Upload video file

		if (!empty($video_filename))
                {

                define('MULTIPART_BOUNDARY', '--------------------------'.microtime(true));
                $header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
                define('FORM_FIELD', 'uploaded_file');
                $filename = "/var/dejavufiles/captures/$video_filename";
                $file_contents = file_get_contents($filename);

                $content =  "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
                "Content-Type: video/mp4\r\n\r\n".
                $file_contents."\r\n";

                // add some POST fields to the request too: $_POST['foo'] = 'bar'
                $content .= "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; name=\"auth_key\"\r\n\r\n".
                "$apikey\r\n";

                // signal end of request (note the trailing "--")
                $content .= "--".MULTIPART_BOUNDARY."--\r\n";
                $context = stream_context_create(array(
                        'http' => array(
                        'method' => 'POST',
                        'header' => $header,
                        'content' => $content,
                        ),
			"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false)
                ));
                $result = file_get_contents($uploadurl, false, $context);
                unlink("/var/dejavufiles/captures/$video_filename");

                }	


		//Upload msg file

                if (!empty($msg_filename))
                {

                define('MULTIPART_BOUNDARY', '--------------------------'.microtime(true));
                $header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
                define('FORM_FIELD', 'uploaded_file');
                $filename = "/var/dejavufiles/captures/$msg_filename";
                $file_contents = file_get_contents($filename);

                $content =  "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
                "Content-Type: message/rfc822\r\n\r\n".
                $file_contents."\r\n";

                // add some POST fields to the request too: $_POST['foo'] = 'bar'
                $content .= "--".MULTIPART_BOUNDARY."\r\n".
                "Content-Disposition: form-data; name=\"auth_key\"\r\n\r\n".
                "$apikey\r\n";

                // signal end of request (note the trailing "--")
                $content .= "--".MULTIPART_BOUNDARY."--\r\n";
                $context = stream_context_create(array(
                        'http' => array(
                        'method' => 'POST',
                        'header' => $header,
                        'content' => $content,
                        ),
                        "ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false)
                ));
                $result = file_get_contents($uploadurl, false, $context);
                unlink("/var/dejavufiles/captures/$msg_filename");

                }      


	}

	else{
		saveLog($decoyName, $decoyGroup, $decoyIP, $attackerIP, $eventType, $eventCategory, $timestamp, $rawlog, $serviceType, $decoy_type, $pcap_filename, $video_filename, $msg_filename);
	}

}

if($SyslogStatus == 1)
{
	$syslog = "decoyname=".$decoyName." | decoygroup=".$decoyGroup." | decoyip=".$decoyIP." | servicetype=".$serviceType." | decoytype=".$decoy_type." | attackerip=".$attackerIP." | eventtype=".$eventType." | logtime=".$logtime." | pcapfilename=".$pcap_filename." | videofilename=".$video_filename." | msgfilename=".$msg_filename;
	
	$myfile = file_put_contents('/var/log/syslogclient.log', $syslog.PHP_EOL , FILE_APPEND | LOCK_EX);
}

?>

