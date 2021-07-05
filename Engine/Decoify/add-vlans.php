<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

  if (isset($_POST['listvlans']) && $_SESSION['csrf_token'] == $_POST['csrf_token'])
  {
  if(val_input($_POST["interface"])){
      $interface=$_POST["interface"];
  }
  else{
    echo "<script>
    alert('Interface value Invalid');
    window.location.href='add-vlans.php';
    </script>";
    exit();
  }
  exec("sudo /usr/bin/tshark -a duration:10 -i $interface -Y \"vlan\" -x -V 2>&1 |grep -o \" = ID: .*\" |awk -F \"ID: \" '{print$2}' | sort --unique | xargs |sed \"s/ /, /g\"",$vlanoutput,$vlanresult);
  $vlans=$vlanoutput[0];
  }


  if ($_POST['vlanid'] && $_SESSION['csrf_token'] == $_POST['csrf_token'])
  {

  if(val_input($_POST["interface"])){
      $interface=$_POST["interface"];
  }
  else{
    echo "<script>
    alert('Interface value Invalid');
    window.location.href='add-vlans.php';
    </script>";
    exit();
  }

  if(val_input($_POST["vlanid"])){
      $vlanid=$_POST["vlanid"];
  }
  else{
    echo "<script>
    alert('Please enter valid vlanid');
    window.location.href='add-vlans.php';
    </script>";
    exit();
  }

  exec("sudo /sbin/vconfig add $interface $vlanid");
  exec("sudo /sbin/ifconfig $interface.$vlanid up");
  echo "<script>
  alert('VLAN ID $vlanid is added to $interface.');
  </script>";

  }

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
        Add VLANs 
        <small>Add</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">VLAN Management</a></li>
        <li class="active">Add VLANs</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">New VLAN</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="add-vlans.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
                <div class="form-group" style="width: 150px">
                  <label>Physical Interface</label>
                  <select class="form-control" name="interface">
                    <?php

		      exec('sudo /sbin/ifconfig | grep "flags="| egrep -v "veth|lo|virtual|docker|eth0|\."|awk -F ":" \'{print$1}\'',$output,$result);
                      for($i=0;$i<sizeof($output);$i++) {
                        echo '<option>'.dataFilter($output[$i]).'</option>';
      
                      }
                      
                    ?>
                  </select>
                </div>
                <!-- text input -->


		<div class="form-group">
                  <label>Add VLAN ID</label>
                  <input type="text" class="form-control" name="vlanid" placeholder="VLAN ID" style="width: 150px">
		  <br> 
		  <button type="submit" name="listvlans" value="Yes" class="btn btn-primary">List Available VLANs</button>
		  <br><br>
		  <?php
			echo "<label>Identified VLANs: </label>  ".dataFilter($vlans);
		  ?>
		  <br>
		  <br>
		  <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Add VLAN</button>
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
