<?php

if(!isset($_SESSION)) 
{  
  session_start();
}

include 'db.php';


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
  if(isset($_POST['honeyfilesdomain']) && isset($_POST['remindernote']))
  {
	$fileid = strtoupper(uniqid());
  	$honeyfilesdomain = htmlspecialchars($_POST['honeyfilesdomain']);
  	$remindernote = htmlspecialchars($_POST['remindernote']);

	copy("/var/log/data/honeyfile.docx", "/var/log/data/$fileid.docx");

	$zip = new ZipArchive();
	$inputfilename = "/var/log/data/$fileid.docx";
	$token1 = "http://".$honeyfilesdomain."/".$fileid.".aspx";

	if ($zip->open($inputfilename, ZipArchive::CREATE)!==TRUE) {
    		echo "Cannot open $inputfilename :( "; die;
	}
	$xml = $zip->getFromName('word/footer2.xml');
	$xml = str_replace('DEJAVUHONEYCOMB', $token1, $xml);

	$zip->addFromString('word/footer2.xml', $xml);
	$zip->close();

        $zip = new ZipArchive();
        $inputfilename = "/var/log/data/$fileid.docx";
        $token1 = "http://".$honeyfilesdomain."/".$fileid.".aspx";

        if ($zip->open($inputfilename, ZipArchive::CREATE)!==TRUE) {
                echo "Cannot open $inputfilename :( "; die;
        }
        $xml = $zip->getFromName('word/_rels/footer2.xml.rels');
        $xml = str_replace('DEJAVUHONEYCOMB', $token1, $xml);

        $zip->addFromString('word/_rels/footer2.xml.rels', $xml);
        $zip->close();


        $mysqli = db_connect();
        $stmt = $mysqli->prepare("INSERT into HoneyFiles (Fileid,Server,Note) VALUES(?,?,?)");
        if (!$stmt) {
           throw new Exception('Error in preparing statement: ' . $mysqli->error);
        }
        $stmt->bind_param("sss", $fileid,$honeyfilesdomain,$remindernote);
        $stmt->execute();
        $stmt->close();


	
	$name = "/var/log/data/$fileid.docx";
	$fp = fopen($name, 'rb');

	header("Content-Disposition: attachment; filename=\"honeyfile_$fileid.docx\"");
	header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
	header("Content-Length: " . filesize($name));

	fpassthru($fp);
	unlink("/var/log/data/$fileid.docx");
	exit;


  }
  
}



require 'honeyfilesView.php';

?>
