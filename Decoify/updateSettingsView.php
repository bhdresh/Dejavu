<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

?>

  <!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Modify Settings
      </h1>
      <?php if(isset($_GET["config"]) && $_GET["config"] == 'success')
          {
          ?>
            <p class="text-red">Previous configuration restore successful</p>
            <p class="text-red">Reboot to apply changes. Please note the updated Management Interface : <?= $_GET["interface"];?> </p>
            <form action="updateSettings.php" method="post">
              <input type="hidden" name="reboot" value="1">
             <button type="submit" class="btn btn-primary">Reboot</button>
            </form>
          <?php
          }
          ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Modify Settings</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          
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
              <h3 class="box-title">Update Management Interface</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
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
              <h3 class="box-title">Update SMTP Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
              <?php
                
                  //Get SMTP Info

              $mysqli = db_connect();
  
   
              $stmt = $mysqli->prepare("SELECT Hostname, Username, Password FROM SMTPDetails");
  
              $stmt->execute();

              $result = $stmt->get_result();

	      $row = mysqli_fetch_assoc($result);

              $stmt->close();

              ?>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="smtp_hostname" class="form-control" placeholder="SMTP Server Name/IP (Optional)" value="<?=$row['Hostname'] ?>">
                <span class="glyphicon fa  fa-globe form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="smtp_username" class="form-control" placeholder="SMTP Username (Optional)" value="<?=$row['Username'] ?>">
                <span class="glyphicon fa fa-user-secret form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="password" name="smtp_password" class="form-control" placeholder="SMTP Password (Optional)" value="<?=$row['Password'] ?>">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              </div>
              <br>
      
              <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Update</button>
                </div>
              </form>
              </div>
              <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Syslog Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
              <?php
                
                  //Get Syslog Info

              $mysqli = db_connect();
  
   
              $stmt = $mysqli->prepare("SELECT IP, PORT, Protocol FROM SyslogDetails where Status='1'");
  
              $stmt->execute();

              $result = $stmt->get_result();

	      $row = mysqli_fetch_assoc($result);

              $stmt->close();

              ?>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="syslog_server_ip" class="form-control" placeholder="Syslog Server IP (Optional)" value="<?=$row['IP'] ?>">
                <span class="glyphicon fa fa-laptop form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="syslog_port" class="form-control" placeholder="Syslog Server Port (Optional)" value="<?=$row['PORT'] ?>">
               <span class="glyphicon fa fa-laptop form-control-feedback"></span></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <select class="form-control" name="protocol">
                  <option value="TCP" name="sylog_service">TCP</option>
                  <option value="UDP" name="sylog_service">UDP</option>
                </select>
              </div>
     	      <label>
              <?php

              //Get SyslogServer Info

                $mysqli = db_connect();
                $stmt = $mysqli->prepare("SELECT * FROM SyslogServerDetails;");
                $stmt->execute();
                $result = $stmt->get_result();
                if(mysqli_num_rows($result) === 0) {
              	echo "<input type=\"checkbox\" class=\"flat-red\" name=\"SyslogServer\">";
		} else {
		echo "<input type=\"checkbox\" class=\"flat-red\" name=\"SyslogServer\" checked>";
		}
		?>	
                 Enable log aggregation service
              </label>
		<br><br>
 
              <button type="submit" name="addsyslog" value="Yes" class="btn btn-primary">Update</button>
                </div>
              </form>
              </div>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Backup/Restore DejaVu Configuration</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div>
          <form action="updateSettings.php" method="post">
            <input type="hidden" name="backup" value="1">
            <button type="submit" class="btn btn-warning">
                Download Backup
            </button>
          </form>
          </div>
          <p>&nbsp;</p>
          
            <form role="form" action="updateSettings.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                     <input type="hidden" name="file_true" value="1">
                     <input type="file" name="fileToUpload" id="fileToUpload">
                </div>
                <button type="submit" class="btn btn-primary">Restore Backup</button>
            </form>
          </div>
        </div>
        <!-- /.box-body -->
        <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Upgrade DejaVu (Current Version : 
            <?php 
            $config = parse_ini_file('config/config.ini'); 
            echo $config['currentVersion'];
            ?>
          )
      </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          
            <form role="form" action="updateFramework.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                     <input type="hidden" name="file_true" value="1">
                     <input type="file" name="fileToUpload" id="fileToUpload">
                </div>
                <button type="submit" class="btn btn-primary">Upgrade DejaVu</button>
            </form>
          </div>
        </div>
          </div>

        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
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
