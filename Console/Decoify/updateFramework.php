<?php

if(!isset($_SESSION)) 
{  
	session_start();
}
require_once('includes/common.php');

include 'db.php';

if(isset($_SESSION['user_name']) && isAuthorized($_SESSION))
{

	if(isset($_POST['file_true']) && $_SESSION['csrf_token'] == $_POST['csrf_token'])
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
			autoupgrade();

		}
		
	}

	header('location:backupSettings.php');
	exit();

}

else{
	header('location:backupSettings.php');
	exit();
}


function autoupgrade()
{
          exec("sudo /etc/applyupgrade.sh",$outputinstall,$resultinstall);

          if($resultinstall === 1)
          {

                 echo "<script>
                 alert('Upgrade successful !!!');
                 window.location.href='backupSettings.php';
                 </script>";
                  exit();

                } else {

                 echo "<script>
                 alert('ERROR: Upgrade failed !!!');
                 window.location.href='backupSettings.php';
                 </script>";
                 exit();
                }


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

		exec("sudo /etc/applyupgrade.sh",$outputinstall,$resultinstall);
		
		if($resultinstall === 1)
		{

			echo "<script>
      				alert('Upgrade successful !!!');
      				window.location.href='backupSettings.php';
      				</script>";
      				exit();


		} else {

			echo "<script>
                                alert('ERROR: Upgrade failed !!!');
                                window.location.href='backupSettings.php';
                                </script>";
                                exit();



		}

		
		exit();
	}
}

?>
