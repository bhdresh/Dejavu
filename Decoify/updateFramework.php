<?php

if(!isset($_SESSION)) 
{  
	session_start();
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{

	if(isset($_POST['file_true']))
	{
		$path = pathinfo($_FILES['fileToUpload']['name']);

		if($path['extension'] == 'zip')
		{
			$file_name = 'upgrade.zip';

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


function uploadFiles($file_name, $tmp_name)
{
	exec("sudo rm -r /var/dejavufiles/Framework/*");

	$filename = $file_name;

	//This needs to be modified
	$target_dir = "/var/dejavufiles/Framework/";

	$target_file = $target_dir . $filename;

	
	if(move_uploaded_file($tmp_name, $target_file)) {

		exec("sudo unzip -o /var/dejavufiles/Framework/upgrade.zip -d /var/dejavufiles/Framework/");

		exec("sudo /bin/sh /var/dejavufiles/Framework/install.sh");

		header('location:updateSettingsView.php');
		
		exit();
	}
}

?>
