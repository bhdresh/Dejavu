<?php

	$server_check_version = '1.0.4';
	$start_time = microtime(TRUE);

	$operating_system = PHP_OS_FAMILY;

	if ($operating_system === 'Windows') {
		// Win CPU
		$wmi = new COM('WinMgmts:\\\\.');
		$cpus = $wmi->InstancesOf('Win32_Processor');
		$cpuload = 0;
		$cpu_count = 0;
		foreach ($cpus as $key => $cpu) {
			$cpuload += $cpu->LoadPercentage;
			$cpu_count++;
		}
		// WIN MEM
		$res = $wmi->ExecQuery('SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem');
		$mem = $res->ItemIndex(0);
		$memtotal = round($mem->TotalVisibleMemorySize / 1000000,2);
		$memavailable = round($mem->FreePhysicalMemory / 1000000,2);
		$memused = round($memtotal-$memavailable,2);
		// WIN CONNECTIONS
		$connections = shell_exec('netstat -nt | findstr :80 | findstr ESTABLISHED | find /C /V ""'); 
		$totalconnections = shell_exec('netstat -nt | findstr :80 | find /C /V ""');
	} else {
		// Linux CPU
		$load = sys_getloadavg();
		$cpuload = $load[0];
		// Linux MEM
		$free = shell_exec('free');
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); }); // removes nulls from array
		$mem = array_merge($mem); // puts arrays back to [0],[1],[2] after 
		$memtotal = round($mem[1] / 1000000,2);
		$memused = round($mem[2] / 1000000,2);
		$memfree = round($mem[3] / 1000000,2);
		$memshared = round($mem[4] / 1000000,2);
		$memcached = round($mem[5] / 1000000,2);
		$memavailable = round($mem[6] / 1000000,2);
		// Linux Connections
		$connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
		$totalconnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
	}

	$memusage = round(($memused/$memtotal)*100);



	$phpload = round(memory_get_usage() / 1000000,2);

	$diskfree = round(disk_free_space(".") / 1000000000);
	$disktotal = round(disk_total_space(".") / 1000000000);
	$diskused = round($disktotal - $diskfree);

	$diskusage = round($diskused/$disktotal*100);

	if ($memusage > 85 || $cpuload > 85 || $diskusage > 85) {
		$trafficlight = 'red';
	} elseif ($memusage > 50 || $cpuload > 50 || $diskusage > 50) {
		$trafficlight = 'orange';
	} else {
		$trafficlight = '#2F2';
	}

	$end_time = microtime(TRUE);
	$time_taken = $end_time - $start_time;
	$total_time = round($time_taken,4);

	// use servercheck.php?json=1
	if (isset($_GET['json'])) {
		echo '{"ram":'.$memusage.',"cpu":'.$cpuload.',"disk":'.$diskusage.',"connections":'.$totalconnections.'}';
		exit;
	}

?>

<style>
		#container {
background: #FFF;
		font-family: Arial,sans-serif;
		margin: 0;
		padding: 0;
		color: #333;
	
		margin: 10px auto;
		padding: 10px 20px;
		background: #EFEFEF;
		border-radius: 5px;
		box-shadow: 0 0 5px #AAA;
		-webkit-box-shadow: 0 0 5px #AAA;
		-moz-box-shadow: 0 0 5px #AAA;
		box-sizing: border-box;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
	}
	.description {
		font-weight: bold;
	}
	#trafficlight {
		float: right;
		margin-top: 15px;
		width: 50px;
		height: 50px;
		border-radius: 50px;
		background: <?php echo $trafficlight; ?>;
		border: 3px solid #333;
	}
	#details {
		font-size: 0.8em;
	}
	hr {
		border: 0;
		height: 1px;
		background-image: linear-gradient(to right, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0));
	}
	.big {
		font-size: 1.2em;
	}
	.footer {
		font-size: 0.5em;
		color: #888;
		text-align: center;
	}
	.footer a {
		color: #888;
	}
	.footer a:visited {
		color: #888;
	}
	.dark {
		background: #000;
		filter: invert(1) hue-rotate(180deg);
	}
	</style>
  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>V</b>u</span>
      <!-- logo for regular state and mobile devices -->
     <span class="logo-lg"><b>DejaVu | </b>Engine<span style="font-size: 12px;"></span></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
	<?php
if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin'){ 
?>
	<li class="dropdown user user-menu">

        <a class="nav-link" data-toggle="dropdown" href="#">
          Heath Monitor
        </a>
<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
<div id="container">
		<div id="trafficlight" class="nodark"></div>

		<p><span class="description big">RAM Usage:</span><div class="progress progress-xs">
                          <div class="progress-bar progress-bar-danger" style="width: <?php echo $memusage; ?>%"></div>
                        </div> <span class="result big"><?php echo $memusage; ?>%</span></p>
		<p><span class="description big">CPU Usage: </span> 
<div class="progress progress-xs">
                          <div class="progress-bar progress-bar-danger" style="width: <?php echo $cpuload; ?>%"></div>
                        </div>
<span class="result big"><?php echo $cpuload; ?>%</span></p>
		<p><span class="description big">Hard Disk Usage: </span>
<div class="progress progress-xs">
                          <div class="progress-bar progress-bar-danger" style="width: <?php echo $diskusage; ?>%"></div>
                        </div>

 <span class="result"><?php echo $diskusage; ?>%</span></p>
			</div>   
<div class="dropdown-divider"></div>
          <a href="healthcheck.php" class="dropdown-item dropdown-footer">View More</a>			
</div>
      </li>
<?php } ?>
          <!-- User Account: style can be found in dropdown.less -->
          <script language="javascript"> 
            function DoPost(){
              $(document).ready(function(){
                $("#cloudLogin").submit();
              });
            }
          </script>

<script>
$(document).ready(function(){ 
<?php 
if(GetApiStatus() == 1){
?>
  $.ajax({url: "updateSettings.php", data: "check=api&csrf_token=<?php echo $_SESSION['csrf_token']; ?>", success: function(result){
  var api_value;
  api_value = result;
  if(api_value == "<p class=\"text-red\">Connection to DejaVu seems to be okay. However, the API key is not a valid one. Update your API key below and try again!</p>" || api_value == "<p class=\"text-red\">API Connection Failed! Check API KEY and ensure connection to DejaVu Console!</p>"){
    
    $('#cloud_fail').css("display", "block");
  }
  else{
    $('#cloud_success').css("display", "block");
  }  
}});
<?php
}
?>
  $.ajax({url: "getCount.php", data: "count=eventCount", success: function(result2){
      var api_value2;
      api_value2 = result2;
      $("#active_event_count").text(api_value2);
      var int_count = parseInt(api_value2);
      if(int_count > 0){
        $("#event_count").addClass( "alert-danger" );
      }
    }});
});
</script> 

<div class="modal modal-danger fade in" id="api-modal-warning" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">DejaVu Console API Connection Failed</h4>
      </div>
      <div class="modal-body">
        <p>Connection to DejaVu Console cannot be established. Logs cannot be viewed on the dashboard. <br/>
It is recommended to enable outbound connection to DejaVu Console"</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="location.href='updateSettingsView.php'">Check Connection Again</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<!-- API CAll Check End -->
          <li id="cloud_success" style="display: none;">
            <a href="cloudSettings.php#api-connection-check">
            <i class="fa fa-circle text-green"></i>
              <span class="hidden-xs">Console Connection</span>
            </a>
          </li>
          <li id="cloud_fail" style="display: none;">
            <a href="cloudSettings.php#api-connection-check">
            <i class="fa fa-circle text-red"></i>
              <span class="hidden-xs">Console Connection</span>
            </a>
          </li>
          <li id="event_count">
            <a href="javascript:DoPost()">
              <i class="fa fa-fw fa-gears"></i>
              <span class="hidden-xs">DejaVu Console (<div style="display:inline;" id="active_event_count"></div>)</span>
            </a>
          </li>
          <li class="dropdown user user-menu">

            <a href="logout.php" >
              <span class="hidden-xs">Sign Out</span>
            </a>

          <form action="https://<?= GetConsole_IP()?>/Decoify/login.php" method="post" id="cloudLogin" target="_blank">
              <input type="hidden" name="api_key" value="<?php if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') { echo GetApiKey();} ?>" >
          </form>

          </li>
          <!-- Control Sidebar Toggle Button -->
          
        </ul>
      </div>
    </nav>
  </header>

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
