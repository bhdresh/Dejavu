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
        Add Files 
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
              <h3 class="box-title">Add Custom Decoy Files</h3>
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
            <?php if(isset($_GET["msg"]) && $_GET["msg"] == 'fail')
              {
              ?>
                <p class="text-red">There was some with file upload. Please try again.</p>
              <?php
              }
              if(isset($_GET["msg"]) && $_GET["msg"] == 'success')
              {
            ?>
             <p class="text-green">File Uploaded Sucessfully</p>
            <?php
              }
              if(isset($_GET["msg"]) && $_GET["msg"] == 'invalidfile')
              {
            ?>
              <p class="text-red">Only Zip files allowed</p>
            <?php
              }
            ?>
              <form action="addFiles.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
		<div class="form-group" style="width: 150px">
                  <label>File Name</label>
                  <input type="text" class="form-control" name="file_name" placeholder="File Name" style="width: 150px">
                </div>
                <!-- text input -->
		          <div class="form-group">
                 <input type="file" name="fileToUpload" id="fileToUpload">
                 <p class="text-red">Zip files only</p>
		          <br> 
		  <button type="submit" name="addfiles" value="Yes" class="btn btn-primary">Add File</button>

                </div>
              </div>
              </form>
            </div>
            <!-- /.box-body -->
            <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List Uploaded Files</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered">
                <tbody>
                <tr>
                  <th>File Name</th>
                  <th style="width: 200px">Manage File</th>
                </tr>
                <tr>
                  <td>OWA Login</td>
                  <td><a href="#"> Default Page</a></td>
                </tr>
                <tr>
                  <td>F5 Login</td>
                  <td><a href="#"> Default Page</a></td>
                </tr>
              <?php if(!empty($event)){
                  foreach ($event as $key => $value): 
              ?>
                <tr>
                  <td><?= dataFilter($event[$key]['file_name'])?></td>
                  <td><a href="addFiles.php?del_id=<?=dataFilter($event[$key]['id'])?>&csrf_token=<?php echo $_SESSION['csrf_token']?>"> Delete File <span class="glyphicon glyphicon-remove"></span></a></td>
                </tr>
              <?php endforeach;
                }
              ?>
              </tbody></table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>
          </div>
          <!-- /.box -->
          <!-- /.box -->
        </div>
          </div>
          <!-- /.box -->
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
