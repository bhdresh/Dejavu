<?php

//Connection to database

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


  if(isset($_SESSION['user_name']) &&  $_SESSION['role'] == 'admin')
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

// Validate IP 

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

//CheckAPI Connection

function check_alive() {

  $Console_IP = GetConsole_IP();

  $url = "https://".$Console_IP."/Decoify/connection_check.php";

  $apikey = GetApiKey();

  //new code

  $data = array('auth_key' => $apikey);

	$options = array(
		"ssl"=>array(
     		"verify_peer"=>false,
      		"verify_peer_name"=>false,
    		),
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\nConnection: close\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
      'timeout' => 2
    )
	);

  $context  = stream_context_create($options);

  $result = file_get_contents($url, false, $context);
 
  return $result;
}

//Check number of events

function check_activeevents() {

  $Console_IP = GetConsole_IP();

  $url = "https://".$Console_IP."/Decoify/getEventCount.php";

  $apikey = GetApiKey();

  //new code

  $data = array('auth_key' => $apikey);

	$options = array(
		"ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
                ),
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\nConnection: close\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
      'timeout' => 2
    )
	);

  $context  = stream_context_create($options);

  $result = file_get_contents($url, false, $context);
  
  return $result;
}

//CheckAPI Connection

function check_api_connection() {

  $Console_IP = GetConsole_IP();

  $url = "https://".$Console_IP."/Decoify/connection_check.php";

  //new code

  $data = array('auth_key' => $apikey);

	$options = array(
		"ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
                ),
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\nConnection: close\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
      'timeout' => 2
    )
	);

  $context  = stream_context_create($options);

  $result = file_get_contents($url, false, $context);
  
  return $result;
}

//Get API Key

function GetApiKey()
{
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT ApiKey FROM Users Limit 1;");

  $stmt->execute();

  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $event[] = $row;
  }

  $apikey = $event[0]['ApiKey'];

  return $apikey;
}

//Get Console IP

function GetConsole_IP()
{
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT Console_IP FROM Users Limit 1;");

  $stmt->execute();

  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $event[] = $row;
  }

  $Console_IP = $event[0]['Console_IP'];

  return $Console_IP;
}

//Get API Status

function GetApiStatus()
{
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT EnableApi FROM Users Limit 1;");

  $stmt->execute();

  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $event[] = $row;
  }

  $apikey = $event[0]['EnableApi'];

  return $apikey;
}

function SyslogStatus()
{
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT Status FROM SyslogDetails Limit 1;");

  $stmt->execute();

  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $event[] = $row;
  }

  $SyslogStatus = $event[0]['Status'];

  return $SyslogStatus;
}

?>
