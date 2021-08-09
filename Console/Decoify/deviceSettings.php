<?php

if(!isset($_SESSION)) 
{ 
    session_start();
}

include "db.php";
if(!isset($_SESSION['user_name']) && $_SESSION['role'] != 'admin')
{
        header('location:loginView.php');
        exit();

}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

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
                    <input type="text" name="PortNumber" class="form-control" placeholder="SMTP PortNumber. Default Value 443" value="<?=$PortNumber; ?>">
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