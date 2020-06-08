<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

if (isset($_POST['generatePS']))
{
if ($_POST['username'] == false || $_POST['password'] == false || $_POST['domainname'] == false)
{
echo "<script>
alert('Please specify the Username, Password and Domain Name!');
window.location.href='crumbHash.php';
</script>";
exit();
} else {

$username=$_POST['username'];
$password=$_POST['password'];
$domainname=$_POST['domainname'];

$path = '/var/log/data/honeyhash.ps1';
$file_contents = file_get_contents($path);
$file_contents = str_replace("DEJAVUDOMAIN","$domainname",$file_contents);
$file_contents = str_replace("DEJAVUUSER","$username",$file_contents);
$file_contents = str_replace("DEJAVUPASSWORD","$password",$file_contents);
file_put_contents('/var/log/data/honeyhash.ps1.new',$file_contents);

$name = '/var/log/data/honeyhash.ps1.new';
$fp = fopen($name, 'rb');

header('Content-Disposition: attachment; filename="DejavuHoneyHash.ps1"');
header("Content-Type: application/octet-stream");
header("Content-Length: " . filesize($name));

fpassthru($fp);
exit;


}

}
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
        Create HoneyHash Account 
        <small>Add</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Decoy Management</a></li>
        <li class="active">Add Files</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">HoneyHash Account Details</h3>
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <form action="crumbHash.php" method="post" enctype="multipart/form-data">
                <div class="form-group" style="width: 150px">
                  <label>User Name</label>
                  <input type="text" class="form-control" name="username" placeholder="DomainAdmin" style="width: 150px" required />
                </div>
                <div class="form-group" style="width: 150px">
                  <label>Password</label>
                  <input type="text" class="form-control" name="password" placeholder="MyPassword" style="width: 150px" required />
                </div>
                <div class="form-group" style="width: 150px">
                  <label>Domain Name</label>
                  <input type="text" class="form-control" name="domainname" placeholder="mydomain.com" style="width: 150px" required />
                </div>
                <!-- text input -->
              <div class="form-group">
              
              <button type="submit" name="generatePS" value="Yes" class="btn btn-primary"> Generate Powershell Script</button>
              </div>
              <p class="text-red">Note : Generated powershell script can be deployed using GPO. Monitor for failed logins for Honey Accounts.</p>
              </div>
              </form>
            </div>
            <!-- /.box-body -->
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

