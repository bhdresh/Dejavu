<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}
require_once('includes/common.php');

if(isset($_SESSION['user_name']) && isAuthorized($_SESSION)){

$file_dir = "../../../download_files/";
//provide dejavu download link
$file = $file_dir . 'Mirage.zip';

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
} else {
    header('location:loginView.php');
}
?>
