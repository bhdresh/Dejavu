<?php

include 'db.php';

if (isset($_REQUEST['auth_key']))
{
    $auth_key = $_REQUEST['auth_key']; 
    if(!empty($auth_key))
    {
        //To get user_id
        $mysqli = db_connect();
        $stmt = $mysqli->prepare("SELECT user_id FROM Users WHERE auth_key=? and Status=1;");
        $stmt->bind_param("s", $auth_key);
        $stmt->execute();
        $result = $stmt->get_result();
        
		while($row = $result->fetch_array()) {
            $event[] = $row;
        }
    
        $user_id = $event[0]["user_id"];

        //echo $user_id;
    
        $stmt->close();

        //Get event count

        $mysqli = db_connect();
        $stmt = $mysqli->prepare("select COUNT(Status) as active_events from Alerts where Status=1 and user_id=?;");
        $stmt->bind_param("s", $user_id); 
        $stmt->execute();
        $result2 = $stmt->get_result();

        while($row2 = $result2->fetch_array()) {

            $event2[] = $row2;
        
        }

        $count = $event2[0]["active_events"];

        $stmt->close();

        echo $count;
    } 
}

else{

echo "No auth key!";
}
