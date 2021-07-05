<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

?>
<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Decoy Management
        <!-- <small>Decoy List</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Decoy Management</a></li>
        <li class="active">Manage Decoys</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Manage Decoys</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Decoy Name</th>
                  <th>Network Location</th>
		  <th>Interface</th>
                  <th>Services</th>
                  <th>IP Address</th>
	 	  <th>Apache files</th>
		  <th>SMB files</th>
		  <th>Action</th>
                </tr>
                </thead>
                <tbody>


                <?php

                    $mysqli = db_connect();

                    $stmt = $mysqli->prepare("SELECT decoyid, decoyname, decoygroup, interface, services, ipaddress, apachedecoyfile, smbdecoyfile FROM decoys;");
                    
                    $stmt->execute();

                    $result = $stmt->get_result();

                      if (mysqli_num_rows($result) > 0) {
                          // output data of each row
                          while($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                //echo '  <td><a href="manage-decoy.php?decoyname='.dataFilter($row["decoyname"]).'">'.dataFilter($row["decoyid"]).'</a></td>';
                                echo '  <td>'.dataFilter($row["decoyname"]).'</td>';
                                echo '  <td>'.dataFilter($row["decoygroup"]).'</td>';
				echo '  <td>'.dataFilter($row["interface"]).'</td>';
				echo '	<td>'.dataFilter($row["services"]).'</td>';
                                //echo '  <td><span class="label label-success">'.$row["services"].'</span></td>';
                                echo '  <td>'.dataFilter($row["ipaddress"]).'</td>';
				echo '  <td>'.dataFilter($row["apachedecoyfile"]).'</td>';
				echo '  <td>'.dataFilter($row["smbdecoyfile"]).'</td>';
			        echo '  <td><a href="manage-decoy.php?decoyname='.dataFilter($row["decoyname"]).'"><span class="fa fa-trash"></span></td>';
                                echo '</tr>';
                          }
                      }

                      $stmt->close();
                ?> 

                </tbody>
                <tfoot>
		<tr>
                  <th>Decoy Name</th>
                  <th>Network Location</th>
                  <th>Interface</th>
                  <th>Services</th>
                  <th>IP Address</th>
                  <th>Apache files</th>
                  <th>SMB files</th>
		  <th>Action</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
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
