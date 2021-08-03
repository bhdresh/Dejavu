<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

  
  if (isset($_POST['delvlan']) && $_SESSION['csrf_token'] == $_POST['csrf_token'])
  {
  if(val_input($_POST["interface"])){
      $interface=dataFilter($_POST["interface"]);
  }
  else{
    echo "<script>
    alert('Interface value Invalid');
    window.location.href='del-vlans.php';
    </script>";
    exit();
  }

$mysqli = db_connect();
$stmt = $mysqli->prepare("SELECT * FROM decoys WHERE interface=?");
$stmt->bind_param("s", $interface);
$stmt->execute();
$result = $stmt->get_result();

if(mysqli_num_rows($result) === 0) {
exec("sudo /sbin/vconfig rem $interface");
echo "<script>
alert('VLAN Interface: $interface is removed.');
</script>";
} else {
echo "<script>
alert('ERROR: Please remove decoys deployed on $interface first.');
</script>";

}

}

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
        Del VLANs 
        <small>Del</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">VLAN Management</a></li>
        <li class="active">Del VLANs</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Del VLAN</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="del-vlans.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                <div class="form-group" style="width: 150px">
                  <label>Select VLAN Interface</label>
                  <select class="form-control" name="interface">
                    <?php

		      exec('sudo /sbin/ifconfig | grep "flags="| egrep -v "veth|lo|virtual|docker"|awk -F ":" \'{print$1}\'|grep "\."',$output,$result);
                      for($i=0;$i<=sizeof($output);$i++) {
                        echo '<option>'.dataFilter($output[$i]).'</option>';
      
                      }
                      
                    ?>
                  </select>
                </div>
                <!-- text input -->


		<div class="form-group">
		  <button type="submit" name="delvlan" value="Yes" class="btn btn-primary">Del VLAN</button>
                </div>
              </div>
              </form>
            </div>
            <!-- /.box-body -->
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
