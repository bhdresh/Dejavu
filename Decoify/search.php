<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

error_reporting(1);

include 'db.php';

function SearchQuery($startDate, $endDate)
{
	$mysqli = db_connect();
		
	if($startDate != '' and $endDate != '')
	{      
		$startDate = (string)$startDate . ' 00:00:01';

                $endDate = (string)$endDate . ' 23:59:59';

		$stmt = $mysqli->prepare("SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where (TimeStamp between ? and ?)");

		$stmt->bind_param("ss", $startDate, $endDate);
	}
	else
	{
		$stmt = $mysqli->prepare("SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs");
	}
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	//$arr = $result->fetch_assoc();

	while($row = $result->fetch_array()) {

  	$event[] = $row;
	
	}

	$stmt->close();

	return $event;
}

function SearchAlert($attackerIP,$decoyIP)
{
	$mysqli = db_connect();
		
	$stmt = $mysqli->prepare("SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where Attacker_IP=? and Decoy_IP=?");

	$stmt->bind_param("ss", $attackerIP, $decoyIP);
	
	
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	//$arr = $result->fetch_assoc();

	while($row = $result->fetch_array()) {

  	$event[] = $row;
	
	}

	$stmt->close();

	return $event;
}

function AdvanceQuery($vals, $startDate, $endDate)
{
	$filterCont = array(
		'all' => 'and',
		'any' => 'or'
		);

	$criteriaConst = array(
		'decoyName' => 'Decoy_Name',
		'decoyIP' => 'Decoy_IP',
		'attackerIP' => 'Attacker_IP',
		'eventType' => 'EventType',
		'decoyGroup' => 'Decoy_Group'
		);
	
	$query = '';

	$filter = $filterCont[$vals[0]['filter']];

	if($startDate != '' and $endDate != '')
	{
		$startDate = (string)$startDate . ' 00:00:01';

		$endDate = (string)$endDate . ' 23:59:59';
	}	

	$i = 0;

	foreach ($vals[0]['searchQuery'] as $val) {

		$criteria = $val['criteria'];
		$condition = $val['condition'];
		$searchData = $val['search_data'];

		$col = $criteriaConst[$criteria];

		//Get the condition
		if ($condition == 'eq')
		{
			$cond1 = 'or';
			$cond2 = '=';
		}
		elseif ($condition == 'not_eq')
		{
			$cond1 = 'and';
			$cond2 = '<>';
		}

		$search_data_ar = explode(',', $searchData);

		foreach ($search_data_ar as $key => $value) {
		
		if($key==0)
		{
			$query .= " ".$filter." (".$col." ".$cond2." ?";
		}
		else
		{
			$query .= " ".$cond1." ".$col." ".$cond2." ?";
		}

		$a_params[] = $value;
    	//Binding parameters. Types: s = string, i = integer
    	$a_param_type[] = "s";
		}
		$query .= ")";
		//end query

	}

    $mysqli = db_connect();

	//appending the query based on and filter
	if ($filter == 'and')
	{
		$search_query = "SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where 1".$query. "ORDER BY timestamp Desc ";
		if($startDate != '' and $endDate != '')
		{
			$search_query = "SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where (TimeStamp between ? and ? )".$query. "ORDER BY timestamp Desc"; 
		}
		
	}

	elseif ($filter == 'or') {
		$search_query = "SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where 'true'='false'".$query;
		if($startDate != '' and $endDate != '')
		{
			$search_query = "SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where (TimeStamp between ? and ? ) and (1=2".$query. ")";
		}
	}

	$stmt = $mysqli->prepare($search_query);
	
	if (!$stmt) {
    throw new Exception('Error in preparing statement: ' . $mysqli->error);
	}

	// make $a_param_type a string
	$str_param_type = implode('', $a_param_type);

	// add this string as a first element of array
	array_unshift($a_params,$str_param_type);

	$tmp = array();
	
	foreach ($a_params as $key => $value) {
	    // each value of tmp is a reference to `$a_params` values
	    $query_params[$key] = &$a_params[$key];  
	}

	//this is form query string like -> SELECT Decoy_Name, Decoy_Group, Service_Name, EventType,Attacker_IP, Decoy_IP, TimeStamp FROM MasterLogs where (TimeStamp between ? and ? ) and (Decoy_Name = ?)Array ( [0] => sss [1] => 2018-07-01 00:00:01 [2] => 2018-07-31 23:59:59 [3] => )

	if($startDate != '' and $endDate != '')
	{
		$new_params = 'ss' . $query_params[0];

		array_unshift($query_params, $new_params, $startDate, $endDate);

		array_splice($query_params, 3, 1);
	}

	function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
	}


	// try to call
	call_user_func_array([$stmt,'bind_param'],refValues($query_params));
	
	

	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {
		
		$stmt->close();

		return;
	}

	else{
		while($row = $result->fetch_array()) {

	  	$event[] = $row;
		
		}

		$stmt->close();

		return $event;
	}

}

function insertSearchFilter($search_filter)
{
	//save search string to database

	    $searchFilter = $search_filter;

		$mysqli = db_connect();

		$status = 1;

		$createdDate = date("Y-m-d H:i:s");

		$updatedDate = date("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("Insert Into SearchFilter (search_filter, status, created_date, updated_date) VALUES (?,?,?,?)");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("ssss", $searchFilter, $status, $createdDate, $updatedDate);

		$stmt->execute();

		$stmt->close();

}

function updateSearchFilter($search_filter)
{
	//save search string to database

	    $searchFilter = $search_filter;

		$mysqli = db_connect();

		$updatedDate = date("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("Update SearchFilter set search_filter = ?,updated_date = ? where status = 1");

		if (!$stmt) {
	    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
	    	exit();
		}

		$stmt->bind_param("ss", $searchFilter, $updatedDate);

		$stmt->execute();

		$stmt->close();

}

function checkSearchFilter()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT search_filter from SearchFilter where Status=1;");
	
	$stmt->execute();

	$result = $stmt->get_result();
	
	if($result->num_rows === 0) {

		$row = 0;

		return $row;
	}

	else
	{
		$row = 1;

		return $row;
	}
}

function getSearchFilter()
{
	$mysqli = db_connect();

	$stmt = $mysqli->prepare("SELECT search_filter from SearchFilter where Status=1;");

	$stmt->execute();

	$result = $stmt->get_result();
	
	
	while($row = $result->fetch_assoc()) {
		$event[] = $row;
	}
	
	$search_filter =  $event[0]['search_filter'];

	return $search_filter;

}

function removeSearchFilter()
{
	//save search string to database

	$mysqli = db_connect();

	$updatedDate = date("Y-m-d H:i:s");

	$status = 0;

	$stmt = $mysqli->prepare("Update SearchFilter set updated_date = ?, status = ?");

	if (!$stmt) {
    	throw new Exception('Error in preparing statement: ' . $mysqli->error);
    	exit();
	}

	$stmt->bind_param("si", $updatedDate, $status);

	$stmt->execute();

	$stmt->close();

}


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin'){

	if(isset($_GET["attackerIP"]) and isset($_GET["decoyIP"]))
	{
		$attackerIP = $_GET["attackerIP"];
		$decoyIP = $_GET["decoyIP"];
		$event = SearchAlert($attackerIP,$decoyIP);
		require 'searchView.php';
	}
	else{
		if(isset($_POST["deleteFilter"]))
		{
			if($_POST["deleteFilter"] == 'Yes'){

				removeSearchFilter();

			}
		}
		if(isset($_POST["jsonSearchString"]))
		{
			$vals = json_decode(stripslashes($_POST["jsonSearchString"]), true);
		}
		

		if(isset($_POST["jsonSearchString"]) and $_POST["jsonSearchString"] != '' and $vals[0]['searchQuery'][0]['search_data'] != '')
		{

			$search_filter = $_POST["jsonSearchString"];

			//check if filter saved
			$row = checkSearchFilter();

			if($row == 0)
			{
				insertSearchFilter($search_filter);
			}

			elseif ($row == 1) {
				updateSearchFilter($search_filter);
			}

			if(isset($_POST["startDate"]) && isset($_POST["endDate"]))
			{
				$startDate = $_POST['startDate'];
				$endDate = $_POST['endDate'];
			}
			else
			{
				$startDate = '';
				$endDate = '';
			}
			
			
			$vals = json_decode(stripslashes($_POST["jsonSearchString"]), true);

			$event = AdvanceQuery($vals, $startDate, $endDate);
		}
		else
		{

			if(isset($_POST["startDate"]) && isset($_POST["endDate"]))
			{
				$startDate = $_POST['startDate'];
				$endDate = $_POST['endDate'];
			}
			else
			{
				$startDate = '';
				$endDate = '';
			}
			//check if filter saved
			$row = checkSearchFilter();

			if($row == 0)
			{
				$event = SearchQuery($startDate, $endDate);
			}

			elseif ($row == 1) {

				$search_filter = getSearchFilter();

				$vals = json_decode(stripslashes($search_filter), true);

				$event = AdvanceQuery($vals, $startDate, $endDate);
			}
			
		}

		require 'searchView.php';

	}

	
}
else {
	header('location:loginView.php');
}

?>
