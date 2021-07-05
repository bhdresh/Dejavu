<?php

if(!isset($_SESSION)) 
{  
  session_start();
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin')
{
  //$event = showFiles();
  
}

require 'crumbHashView.php';

?>
