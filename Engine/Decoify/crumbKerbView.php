<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
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
        Create Kerberoast Honey Account 
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
              <h3 class="box-title">Kerberoast Honey Account Details</h3>
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <form action="crumbKerb.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                <div class="form-group" style="width: 200px">
                  <label>Service Account Name</label>
                  <input type="text" class="form-control" name="accountName" placeholder="ServiceName" style="width: 200px" required />
                </div>
                <div class="form-group" style="width: 200px">
                  <label>SPN Name</label>
                  <input type="text" class="form-control" name="spnName" placeholder="ServiceName/SPNName.mydomain.com" style="width: 200px" required>
                </div>
                <!-- text input -->
              <div class="form-group">
                  <button type="submit" name="addfiles" value="Yes" class="btn btn-primary"> Generate Powershell Script</button>
              </div>
              <p class="text-red">Note : Generated powershell script needs to run on the domain controller to create fake service account associated with SPN value.  Monitor for 4769 events on Domain Controllers, filter these events by ticket encryption type (0x17) and fake service account created.</p>
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
