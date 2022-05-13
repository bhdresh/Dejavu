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
        Backup & Upgrade
      </h1>
      <?php if(isset($_GET["config"]) && $_GET["config"] == 'success')
          {
          ?>
            <p class="text-red">Previous configuration restore successful</p>
            <p class="text-red">Reboot to apply changes. Please note the updated Management Interface : <?php echo preg_replace("/[^0-9\.]/","",$_GET["interface"]);?> </p>
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
        <li class="active">Backup & Upgrade</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    <div class="row">
        
    <div class="col-xs-12">



      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Backup/Restore DejaVu Configuration</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div>
          <form action="updateSettings.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
            <input type="hidden" name="backup" value="1">
            <button type="submit" class="btn btn-warning">
                Download Backup
            </button>
          </form>
          </div>
          <p>&nbsp;</p>

            <form role="form" action="updateSettings.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                <div class="form-group">
                     <input type="hidden" name="file_true" value="1">
                     <input type="file" name="fileToUpload" id="fileToUpload">
                </div>
                <button type="submit" class="btn btn-primary">Restore Backup</button>
            </form>
          </div>
        </div>



          

        <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Upgrade DejaVu Engine</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

	<?php
		$latestversion = `curl "https://camolabs.io/upgrade/upgrade.php?latestversion=check"`;
            	$config = parse_ini_file('config/config.ini');
		$currentversion=$config['currentVersion'];;

		echo "<label>Current Version : <p style=\"color:blue\">$currentversion</p> </label>";
		echo "<br>";
		echo "<label>Latest Version : <p style=\"color:green\">$latestversion</p> </label>";
	?>

          
            <form role="form" action="updateFramework.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                <button type="submit" class="btn btn-primary">Upgrade DejaVu Engine</button>
            </form>
          </div>
        </div>
          </div>

        </div>













        <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Reboot/Shutdown/Reset DejaVu engine</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div>
		<form action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
			<input type="hidden" name="reboot" value="1"/>
            		<button type="submit" class="btn btn-danger" onclick="return confirm('Do you really want to reboot the appliance?');">
                	Reboot
            		</button>
		</form>
          </div>
		<p>&nbsp;</p>
	 <div>
                <form action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                        <input type="hidden" name="shutdown" value="1"/>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Do you really want to shutdown the appliance?');">
                        Shutdown
                        </button>
                </form>
          </div>
		<p>&nbsp;</p>
         <div>
                <form action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                        <input type="hidden" name="reset" value="1"/>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Do you really want to factory reset the appliance?');">
			Factory Reset
                        </button>
                </form>
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
