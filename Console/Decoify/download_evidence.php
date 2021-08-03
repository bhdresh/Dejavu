<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

$submited_csrf_token = preg_replace("/[^0-9a-zA-Z]/","",$_POST["csrf_token"]);

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin'){

	$user_id=$_SESSION['user_id'];
	$file_dir = "/var/dejavufiles/captures/";
	$filename=preg_replace("/[^0-9a-zA-Z\.]/","",basename($_GET["filename"]));
	$file = $file_dir . $filename;

	$mysqli = db_connect();
	$stmt = $mysqli->prepare("select * from CloudLogs where user_id=? and (pcap_filename=? or video_filename=?)");
 	$stmt->bind_param("sss", $user_id, $filename, $filename);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if($result->num_rows === 0) {
		echo "Invalid File Request";
		exit();
	} else {

		if (file_exists($file)) {
    		header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
    		header('Content-Disposition: attachment; filename='.basename($file));
    		header('Expires: 0');
    		header('Cache-Control: must-revalidate');
    		header('Pragma: public');
    		header('Content-Length: ' . filesize($file));
    		ob_clean();
    		flush();
    		readfile($file);
    		exit;
		}
	}
} else {
    header('location:loginView.php');
}
?>
