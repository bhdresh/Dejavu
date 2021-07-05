<?php

include 'db.php';

if(isset($_GET['count']))
{
    session_write_close();
    echo check_activeevents();
}

?>