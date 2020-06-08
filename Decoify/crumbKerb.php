<?php

if(!isset($_SESSION)) 
{  
  session_start();
}

include 'db.php';


function generatePassword($length = 30) {
    $possibleChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
    $password = '';

    for($i = 0; $i < $length; $i++) {
        $rand = rand(0, strlen($possibleChars) - 1);
        $password .= substr($possibleChars, $rand, 1);
    }

    return $password;
}
     


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
  if(isset($_POST['accountName']) && isset($_POST['spnName']))
  {
  	$accountName = htmlspecialchars($_POST['accountName']);
  	$accountPassword = generatePassword();
  	$spnName = htmlspecialchars($_POST['spnName']);

  	$myfile = fopen("/var/log/data/KerbHoney.ps1", "wr") or die("Unable to open file!");
	$txt = "Import-Module ActiveDirectory\n";

	fwrite($myfile, $txt);
	fclose($myfile);

	$txt = "New-ADUser -Name \"".$accountName."\" -SamAccountName \"".$accountName."\" -DisplayName \"".$accountName."\" -ServicePrincipalNames \"".$spnName."\" -AccountPassword (ConvertTo-SecureString \"".$accountPassword."\" -AsPlainText -Force) -Enabled \$True -GivenName \"".$accountName."\" -PasswordNeverExpires \$True\n";

	$myfile = file_put_contents('/var/log/data/KerbHoney.ps1', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);

	$name = '/var/log/data/KerbHoney.ps1';
	$fp = fopen($name, 'rb');

	header('Content-Disposition: attachment; filename="DejavuKerbHoneyAccount.ps1"');
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . filesize($name));

	fpassthru($fp);
	exit;
  }
  
}



require 'crumbKerbView.php';

?>
