<?php

include 'db.php';

if (isset($_REQUEST['auth_key']))
{
    $auth_key = $_REQUEST['auth_key']; 
    if(!empty($auth_key))
    {
        $mysqli = db_connect();
        $stmt = $mysqli->prepare("SELECT * FROM Users WHERE auth_key=?");
        $stmt->bind_param("s", $auth_key);
		$stmt->execute();
        $result = $stmt->get_result();
        
		if (mysqli_num_rows($result) > 0) {
			echo "Valid_Key";
            exit();
        } 
        else {
			echo "Connection Ok! But Invalid API Key!";
			exit();
        }
    }
}
else{

echo "No auth key!";
}
