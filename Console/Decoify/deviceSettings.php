<?php

if(!isset($_SESSION)) 
{ 
    session_start();
}
require_once('includes/common.php');

include "db.php";
if(!isset($_SESSION['user_name']) && !isAuthorized($_SESSION))
{
        header('location:loginView.php');
        exit();

}

if(isset($_SESSION['user_name']) && isAuthorized($_SESSION)) {

?>

  <!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Device Settings
      </h1>
      <?php if(isset($_GET["config"]) && $_GET["config"] == 'success')
          {
          ?>
            <p class="text-red">Previous configuration restore successful</p>
            <p class="text-red">Reboot to apply changes. Please note the updated Management Interface : <?= dataFilter($_GET["interface"]);?> </p>
            <form action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
              <input type="hidden" name="reboot" value="1">
             <button type="submit" class="btn btn-primary">Reboot</button>
            </form>
          <?php
          }
          ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Device Settings</li>
      </ol>
    </section>
   

    <!-- Main content -->
    <section class="content">
    <div class="row">
        
        <div class="col-xs-12">


	
        <div class="box box-primary">
        <div class="box-header with-border">

          <h3 class="box-title">Date and Time Settings</h3>
          
        </div>
        
        <!-- /.box-header -->
        <div class="box-body">
          <?php
          if(isset($_GET["timezone"]) && $_GET["timezone"] == 'success')
          {
          ?>
          <p class="text-blue">Timezone Updated</p>

          <?php
          }
          ?>
          <p class="text-blue"> Current Time is : <?php echo exec(date); ?> </p>
          <form role="form" action="updateSettings.php" method="post">
	  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
          <div class="form-group has-feedback" style="width: 300px">
          <label>Timezone:</label>
	  <br>
          <?php
                
              //Get Timezone

              $mysqli = db_connect();
  
   
              $stmt = $mysqli->prepare("SELECT Timezone FROM Users Limit 1");
  
              $stmt->execute();

              $result = $stmt->get_result();

	            $row = mysqli_fetch_assoc($result);

              $stmt->close();

          ?>
          <select name="timezone" id="timezone">
            <option value="<?php echo $row['Timezone']; ?>" selected><?php echo $row['Timezone']; ?></option>
            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
            <option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
            <option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
            <option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
            <option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
            <option value="America/Anchorage">(GMT-09:00) Alaska</option>
            <option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
            <option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
            <option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
            <option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
            <option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
            <option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
            <option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
            <option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
            <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
            <option value="America/Havana">(GMT-05:00) Cuba</option>
            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
            <option value="America/Caracas">(GMT-04:30) Caracas</option>
            <option value="America/Santiago">(GMT-04:00) Santiago</option>
            <option value="America/La_Paz">(GMT-04:00) La Paz</option>
            <option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
            <option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
            <option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
            <option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
            <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
            <option value="America/Araguaina">(GMT-03:00) UTC-3</option>
            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
            <option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
            <option value="America/Godthab">(GMT-03:00) Greenland</option>
            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
            <option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
            <option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
            <option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
            <option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
            <option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
            <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
            <option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
            <option value="Asia/Gaza">(GMT+02:00) Gaza</option>
            <option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
            <option value="Asia/Damascus">(GMT+02:00) Syria</option>
            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
            <option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
            <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
            <option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
            <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
            <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
            <option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
            <option value="Australia/Perth">(GMT+08:00) Perth</option>
            <option value="Australia/Eucla">(GMT+08:45) Eucla</option>
            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
            <option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
            <option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
            <option value="Asia/Magadan">(GMT+11:00) Magadan</option>
            <option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
            <option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
            <option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
            <option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
            <option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
          </select>
          </div>

	  <?php

		exec("sudo /bin/cat /etc/systemd/timesyncd.conf|grep -w NTP|awk -F \"=\" '{print$2}'",$outputntp,$result);
                $ntpserver=$outputntp[0];

         ?>

	  <label>NTP Server:</label>
	  <br>
 	  <div class="form-group">
                  <input type="text" class="form-control" name="ntpserver" placeholder="NTP Server" value="<?php echo $ntpserver; ?>" style="width: 200px">
          </div> 

          <button type="submit" name="updatetime" value="Yes" class="btn btn-primary">Update</button>
            </div>
          </form>
      </div>    



	
		
		
		
		
		
              <!-- /.box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Management Interface</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
              <?php
               
		    exec("sudo /sbin/ifconfig eth0| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
		    exec("sudo /sbin/ifconfig eth0| grep -i inet| grep -i netmask|awk -F \" \" '{print$4}'|grep [0-9]",$output4,$result);
		    exec("sudo /sbin/route -n| grep -i eth0|awk -F \" \" '{print$2}'|grep -v \"0.0.0.0\"|grep [0-9]",$output5,$result);
                  
                    $ip = $output1[0];    
                    $mask = $output4[0];  
                    $gateway = $output5[0];

              ?>
             <div class="form-group has-feedback" style="width: 300px">
        <input type="text" name="ipad" class="form-control" placeholder="IP Address" value="<?=$ip; ?>">
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback" style="width: 300px">
        <input type="text" name="mask" class="form-control" placeholder="Subnet Mask" value="<?=$mask; ?>">
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback" style="width: 300px">
        <input type="text" name="gateway" class="form-control" placeholder="Gateway" value="<?=$gateway; ?>">
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
              <br>
      
              <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Update & Reboot</button>
                </div>
              </form>
              </div>

              <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Password</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <?php if(isset($_GET["pass"]) && $_GET["pass"] == 'fail')
              {
              ?>
                <p class="text-red">Invalid Old Password</p>
              <?php
              }

              if(isset($_GET["pass"]) && $_GET["pass"] == 'success')
              {
              ?>
              <p class="text-green">Password Updated</p>
              <?php
              }
              ?>
              <form role="form" action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
              


	           	<div class="form-group">
                  <input type="password" class="form-control" name="oldPassword" placeholder="Old Password" style="width: 150px">
              </div>
              <div class="form-group">
                  <input type="password" class="form-control" name="newPassword" placeholder="New Password" style="width: 150px">
              </div>
              <br>
		  
		          <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Update Password</button>
                </div>
              </div>
              </form>
            </div>
            <!-- /.box-body -->
          </div>

            <!-- /.box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update SMTP Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
		            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
             
               
                
                <?php

                $mysqli = db_connect();
                $stmt = $mysqli->prepare("SELECT * FROM SMTPDetails Limit 1");
                $stmt->execute();
                $result = $stmt->get_result();
                $row = mysqli_fetch_assoc($result);
                $stmt->close();

                $Hostname=$row['Hostname'];
                $Username=$row['Username'];
                $PortNumber=$row['PortNumber'];
                $From_Email=$row['From_Email'];
                
                ?> 
                <div class="form-group has-feedback" style="width: 300px">
                    <input type="text" name="smtp_hostname" class="form-control" required placeholder="SMTP Server IP or Hostname" value="<?=$Hostname; ?>">
                    <span class="glyphicon fa fa-laptop form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback" style="width: 300px">
                    <input type="text" name="From_Email" class="form-control" required placeholder="Set From Email Address" value="<?=$From_Email; ?>">
                    <span class="glyphicon fa fa-laptop form-control-feedback"></span>
                </div>
               
                <div class="form-group has-feedback" style="width: 300px">
                    <input type="text" name="smtp_username" class="form-control" placeholder="SMTP Username" value="<?=$Username; ?>">
                    <span class="glyphicon fa fa-laptop form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback" style="width: 300px">
                    <input type="password" name="smtp_password" class="form-control" placeholder="SMTP Password" value="">
                    <span class="glyphicon fa fa-laptop form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback" style="width: 300px">
                    <input type="text" name="PortNumber" class="form-control" placeholder="SMTP PortNumber. Default Value 25" value="<?=$PortNumber; ?>">
                    <span class="glyphicon fa fa-laptop form-control-feedback"></span>
                </div>
                
              <br>
      
              <button type="submit" name="updateSMTP" value="Yes" class="btn btn-primary">Update SMTP Details</button>
            </form>
            <br/>
            <form method="post" action="updateSettings.php">
            <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="test_emailaddress" class="form-control" required placeholder="Enter email address to test" value="">
                <span class="glyphicon fa fa-laptop form-control-feedback"></span>
            </div>
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/> 
              <input type="hidden" name="test_email" value="test_email"/>  
              <button type="submit" name="TestSMTP" value="Yes" class="btn btn-success">Send Test Email</button>
              <br>
              <b><?php echo dataFilter($_GET['smtp_test']); ?></b>
            </form>
            </div>
              
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update DNS Server</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

		<?php

		exec("sudo /bin/cat /etc/network/interfaces|grep \"dns-nameservers\" |awk -F \"dns-nameservers\" '{print$2}'|xargs",$outputdns,$result);
				
		$dnsserver = $outputdns[0];


         ?>
              <form role="form" action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
		<label>DNS Server IP:</label>
                <div class="form-group">
                  <input type="text" class="form-control" name="pdnsserver" placeholder="DNS Server" value=<?php echo $dnsserver; ?> style="width: 300px">
                </div>
		<input type="hidden" name="updatedns" value="1"/>
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/
              <br>

             <button type="submit" name="updatedns" value="Yes" class="btn btn-primary">Update DNS Setting & Reboot</button>
                </div>
              </div>
              </form>
            </div>
            <!-- /.box-body -->
          </div>



            



            




        </div>
    </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include 'template/main-footer.php';?>
</body>
</html>
<?php
}
else 
{
  header('location:loginView.php');
}
?>
