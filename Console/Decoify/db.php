<?php

//Connection to database
require_once('includes/common.php');

function db_connect() {

    // Define connection as a static variable, to avoid connecting more than once 
    static $connection;

    // Try and connect to the database
    if(!isset($connection)) {
        $config = parse_ini_file('config/config.ini'); 
        $connection = mysqli_connect($config['host'],$config['username'],$config['password'],$config['dbname']);
    }

    // If connection was not successful, handle the error
    if($connection === false) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    return $connection;
}

//Query the database

function db_query($query) {
    // Connect to the database
    $connection = db_connect();

    // Query the database
    $result = mysqli_query($connection,$query);

    return $result;
}

//Check Session

function check_session(){

  if(!isset($_SESSION)) 
  { 
      session_start(); 
  }


  if(isset($_SESSION['user_name']) &&  isAuthorized($_SESSION))
  {
    $logged_in = 'true';
    return $logged_in;
  }

  else
  {
    header('location:loginView.php');
  }

}

// validate input -  string, numeric, space and . --> ^[A-Za-z0-9. ]*$

function val_input($str)
{

  if(preg_match("/^[A-Za-z0-9.\-]*$/", $str)) {
    return true;
  }

  else{
    return false; 
  }

}

// IP Validation

function val_ip($str)
{

  if(preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/',$str)) {
    return true;
  }

  else{
    return false;
  }

}

// Data Filter

function dataFilter($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}

// Get number of active alert count

function activeAlerts()
{

    if(!isset($_SESSION)) 
    { 
        session_start(); 
    }

    $mysqli = db_connect();
    $user_id=$_SESSION['user_id'];
    $stmt = $mysqli->prepare("select COUNT(Status) as active_events from Alerts where Status=1;");
    //$stmt->bind_param("s", $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_array()) {

      $event[] = $row;
    
    }

    $count = $event[0]["active_events"];

    $stmt->close();

    return $count;

}


function get_SMTPDetials()
{
  $mysqli = db_connect();
  $stmt = $mysqli->prepare("SELECT * FROM SMTPDetails Limit 1");
  $stmt->execute();
  $result = $stmt->get_result();
  $smtp = mysqli_fetch_assoc($result);
  $stmt->close();

  return $smtp;
}

?>
