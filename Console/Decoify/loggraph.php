<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

function SearchQuery($startDate, $endDate)
{

$user_id=$_SESSION['user_id'];

	if (strpos($_SERVER["HTTP_REFERER"], 'events.php') !== false) {
        	$mysql_table = 'Alerts';
	} else {
        	$mysql_table = 'CloudLogs';
	}

	$mysqli = db_connect();

		
	if($startDate != '' and $endDate != '')
	{
		$startDate = (string)$startDate . ' 00:00:01';

                $endDate = (string)$endDate . ' 23:59:59';

		$stmt = $mysqli->prepare("select 'source','target','value' union select 'DEJAVU',decoygroup,'10.0' from decoys WHERE (TimeStamp BETWEEN ? AND ?) union select decoygroup,decoyname,'3.0' from decoys WHERE (TimeStamp BETWEEN ? AND ?) union select Attacker_IP,Decoy_Name,'1.5' from $mysql_table WHERE (TimeStamp BETWEEN ? AND ?) and (Status IS NULL or Status <> 0) INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");

		$stmt->bind_param("ssssss", $startDate,$endDate,$startDate,$endDate,$startDate,$endDate);

		$stmt1 = $mysqli->prepare("select Attacker_IP,count(*) from $mysql_table group by Attacker_IP WHERE (TimeStamp BETWEEN ? AND ?) and (Status IS NULL or Status <> 0) INTO OUTFILE 'force1.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'");
		$stmt1->bind_param("ss", $startDate,$endDate);	
	}
	else
	{
		$stmt = $mysqli->prepare("select 'source','target','value' union select 'DEJAVU',decoygroup,'10.0' from decoys union select decoygroup,decoyname,'3.0' from decoys union select Attacker_IP,Decoy_Name,'1.5' from $mysql_table where Status IS NULL or Status <> 0 INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';");

		$stmt1 = $mysqli->prepare("select Attacker_IP,count(*) from $mysql_table where Status IS NULL or Status <> 0 group by Attacker_IP INTO OUTFILE 'force1.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'");
	}
	
	
	$stmt->execute();
	$stmt1->execute();

exec('sudo /bin/cat /var/lib/mysql/dejavu/force.csv | grep "3.0"|awk -F "\"" \'{print$4}\'|xargs|sed "s/^/d.name==\'/g"|sed "s/ /\' \|\| d.name==\'/g"|sed "s/$/\'/g"',$plotoutput,$plotresult);
$decoynames=$plotoutput[0];
exec('sudo /bin/cat /var/lib/mysql/dejavu/force.csv | grep "10.0"|awk -F "\"" \'{print$4}\'|xargs|sed "s/^/d.name==\'/g"|sed "s/ /\' \|\| d.name==\'/g"|sed "s/$/\'/g"',$plotoutput4,$plotresult4);
$decoygroups=$plotoutput4[0];
exec('sudo /bin/cat /var/lib/mysql/dejavu/force1.csv |xargs|sed "s/^/if(d.name==\'/g"|sed "s/ /\' ; if(d.name==\'/g"|sed "s/,/\') return \'Event Count: /g"|sed "s/$/\';/g"',$plotoutput5,$plotresult5);
$decoylogcount=$plotoutput5[0];

exec("sudo /bin/cat /var/www/html/Decoify/render.php.org| sudo /bin/sed \"s/DECOYNAMES@DEJAVU/$decoynames/g\" | sudo /bin/sed \"s/DECOYGROUPS@DEJAVU/$decoygroups/g\" | sudo /bin/sed \"s/DECOYLOGCOUNT@DEJAVU/$decoylogcount/g\" > /var/log/data/render.php",$plotoutput1,$plotresult1);
exec("sudo /bin/mv /var/log/data/render.php /var/www/html/Decoify/render.php",$plotoutput2,$plotresult2);
exec("sudo /bin/mv /var/lib/mysql/dejavu/force.csv /var/www/html/Decoify/force.csv",$plotoutput3,$plotresult3);
exec("sudo /bin/rm /var/lib/mysql/dejavu/force1.csv");

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
		if($startDate != '' and $endDate != '')
		{
			$search_query = "select 'source','target','value' union select 'DEJAVU',Decoy_Group,'10.0' from MasterLogs WHERE (TimeStamp BETWEEN ? AND ?)" .$query. " union select Decoy_Group,Decoy_Name,'3.0' from MasterLogs WHERE (TimeStamp BETWEEN ? AND ?)" .$query. " union select Attacker_IP,Decoy_Name,'1.5' from MasterLogs WHERE (TimeStamp BETWEEN ? AND ?)" .$query. " INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';"; 
		}
		else
		{
			$search_query = "select 'source','target','value' union select 'DEJAVU',Decoy_Group,'10.0' from MasterLogs WHERE 1" .$query. " union select Decoy_Group,Decoy_Name,'3.0' from MasterLogs WHERE 1" .$query. " union select Attacker_IP,Decoy_Name,'1.5' from MasterLogs WHERE 1" .$query. " INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
		}
		
	}

	elseif ($filter == 'or') {
		if($startDate != '' and $endDate != '')
		{
			$search_query = "select 'source','target','value' union select 'DEJAVU',Decoy_Group,'10.0' from MasterLogs where (TimeStamp between ? and ? ) and (1=2".$query. ") union select Decoy_Group,Decoy_Name,'3.0' from MasterLogs where (TimeStamp between ? and ? ) and (1=2".$query. ") union select Attacker_IP,Decoy_Name,'1.5' from MasterLogs where (TimeStamp between ? and ? ) and (1=2".$query. ") INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
		}
		else
		{
			$search_query = "select 'source','target','value' union select 'DEJAVU',Decoy_Group,'10.0' from MasterLogs WHERE 'true'='false'" .$query. " union select Decoy_Group,Decoy_Name,'3.0' from MasterLogs WHERE 'true'='false'" .$query. " union select Attacker_IP,Decoy_Name,'1.5' from MasterLogs WHERE 'true'='false'" .$query. " INTO OUTFILE 'force.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
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

     //three times since $query is repeated thrice

    if($startDate != '' and $endDate != '')
	{
		$new_params = 'ss'. $query_params[0]. 'ss'. $query_params[0]. 'ss'. $query_params[0];
	} 
	else
	{
		$query_params[0] = $query_params[0]. $query_params[0]. $query_params[0];
	}

	if($startDate != '' and $endDate != '')
	{
		array_unshift($query_params, $new_params, $startDate, $endDate);

		array_splice($query_params, 3, 1);
	}

    $itr = 0; 
	foreach ($query_params as $value) {
		if($itr != 0)
		{
			array_push($query_params,$value);
			$new_val[] = $value;
		}
		$itr++;
	}

	foreach ($new_val as $new_val) {
		array_push($query_params,$new_val);
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

exec('sudo /bin/cat /var/lib/mysql/dejavu/force.csv | grep "3.0"|awk -F "\"" \'{print$4}\'|xargs|sed "s/^/d.name==\'/g"|sed "s/ /\' \|\| d.name==\'/g"|sed "s/$/\'/g"',$plotoutput,$plotresult);
$decoynames=$plotoutput[0];
exec('sudo /bin/cat /var/lib/mysql/dejavu/force.csv | grep "10.0"|awk -F "\"" \'{print$4}\'|xargs|sed "s/^/d.name==\'/g"|sed "s/ /\' \|\| d.name==\'/g"|sed "s/$/\'/g"',$plotoutput4,$plotresult4);
$decoygroups=$plotoutput4[0];
exec("sudo /bin/cat /var/www/html/Decoify/render.php.org| sudo /bin/sed \"s/DECOYNAMES@DEJAVU/$decoynames/g\" | sudo /bin/sed \"s/DECOYGROUPS@DEJAVU/$decoygroups/g\" > /var/log/data/render.php",$plotoutput1,$plotresult1);
exec("sudo /bin/mv /var/log/data/render.php /var/www/html/Decoify/render.php",$plotoutput2,$plotresult2);
exec("sudo /bin/mv /var/lib/mysql/dejavu/force.csv /var/www/html/Decoify/force.csv",$plotoutput3,$plotresult3);


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

	require 'logview.php';
}
else {
	header('location:loginView.php');
}

?>
