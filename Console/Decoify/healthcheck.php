<?php

ini_set('memory_limit', '-1');

if(!isset($_SESSION)) 
{ 
    ini_set('session.cookie_samesite', 'None');
    session_start(); 
}
require_once('includes/common.php');

include 'db.php';


if(isset($_SESSION['user_name']) && isAuthorized($_SESSION)){
?>
	<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-black-light sidebar-mini">
<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
<div class="content-wrapper">


<section class="content-header">
      <h1>
        Health Monitor
                
      </h1>
       
    </section>

<section class="content">
<div id="container">
		<div id="trafficlight" class="nodark"></div>

		<p><span class="description big">🌡️ RAM Usage:</span> <span class="result big"><?php echo $memusage; ?>%</span></p>
		<p><span class="description big">🖥️ CPU Usage: </span> <span class="result big"><?php echo $cpuload; ?>%</span></p>
		<p><span class="description big">💽 Hard Disk Usage: </span> <span class="result"><?php echo $diskusage; ?>%</span></p>
		<p><span class="description">🖧 Established Connections: </span> <span class="result"><?php echo $connections; ?></span></p>
		<p><span class="description">🖧 Total Connections: </span> <span class="result"><?php echo $totalconnections; ?></span></p>
		<hr>
		<p><span class="description">🌡️ RAM Total:</span> <span class="result"><?php echo $memtotal; ?> GB</span></p>
		<p><span class="description">🌡️ RAM Used:</span> <span class="result"><?php echo $memused; ?> GB</span></p>
		<p><span class="description">🌡️ RAM Available:</span> <span class="result"><?php echo $memavailable; ?> GB</span></p>
		<hr>
		<p><span class="description">💽 Hard Disk Free:</span> <span class="result"><?php echo $diskfree; ?> GB</span></p>
		<p><span class="description">💽 Hard Disk Used:</span> <span class="result"><?php echo $diskused; ?> GB</span></p>
		<p><span class="description">💽 Hard Disk Total:</span> <span class="result"><?php echo $disktotal; ?> GB</span></p>
		<hr>
		<div id="details">
			<p><span class="description">📟 Server Name: </span> <span class="result"><?php echo $_SERVER['SERVER_NAME']; ?></span></p>
			<p><span class="description">💻 Server Addr: </span> <span class="result"><?php echo $_SERVER['SERVER_ADDR']; ?></span></p>
			<p><span class="description">🌀 PHP Version: </span> <span class="result"><?php echo phpversion(); ?></span></p>
			<p><span class="description">🏋️ PHP Load: </span> <span class="result"><?php echo $phpload; ?> GB</span></p>
			
			<p><span class="description">⏱️ Load Time: </span> <span class="result"><?php echo $total_time; ?> sec</span></p>
		</div>
	</div>

</div>
</section>
<?php
}
else {
	header('location:loginView.php');
}

?>
<script>
	const toggleDarkMode = () => {
		if (localStorage.getItem('darkMode') && localStorage.getItem('darkMode') === 'true') {
			localStorage.setItem('darkMode',false);
		} else {
			localStorage.setItem('darkMode',true);
		}
		setDarkMode();
	}
	const setDarkMode = () => {
		if (localStorage.getItem('darkMode') && localStorage.getItem('darkMode') === 'true') {
			document.documentElement.classList.add('dark');
		} else {
			document.documentElement.classList.remove('dark');
		}
	}
	setDarkMode();
</script>
