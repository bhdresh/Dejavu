<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {
  
  if (isset($_POST['decoyname']))
  {
  $decoyname=$_POST["decoyname"];

  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT * FROM decoydetails WHERE decoyname=?");

  $stmt->bind_param("s", $decoyname);

  $stmt->execute();

  $result = $stmt->get_result();

  if (mysqli_num_rows($result) > 0) {
       // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
	global $decoyroutetable;
        $decoyfullname=$row['decoyname']."_".$row['decoyservicename'];
  	$decoyinternalip=$row['decoyinternalip']."/32";
  	$decoyroutetable=$row['decoyroutetable'];
  	$decoylogfile=$row['decoylogfile'];
        exec("sudo /usr/bin/docker stop $decoyfullname",$output1,$result1);
        exec("sudo /usr/bin/docker rm $decoyfullname",$output2,$result2);
  	exec("sudo /bin/ip rule del from $decoyinternalip table $decoyroutetable",$output3,$result3);
  	exec("sudo /bin/ip link del virtual$decoyroutetable",$output4,$result4);
  	exec("sudo /bin/kill -9 `ps auxx| grep -i \"$decoylogfile\" |awk -F \" \" '{print$2}'`",$output5,$result5);
  	}
        } else {
          echo "0 results";
        }

  $stmt->close();

  $stmt4 = $mysqli->prepare("SELECT * FROM decoys WHERE decoyname=?");

  $stmt4->bind_param("s", $decoyname);

  $stmt4->execute();

  $result = $stmt4->get_result();

  if (mysqli_num_rows($result) > 0) {
       // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
        $decoyip=$row['ipaddress'];
        $ttl=$row['ttl'];
        exec("sudo /sbin/iptables -t mangle -D POSTROUTING -s $decoyip -j TTL --ttl-set $ttl",$outputdelttlentry,$resultttlentry);
	exec("sudo /sbin/iptables -t nat -D PREROUTING -d $decoyip -j LOG",$outputmainlogpid,$resultmainlogpid);
	exec("sudo /bin/ip rule del from $decoyip table $decoyroutetable",$output6,$result6);
        }
        } else {
          echo "0 results";
        }

  $stmt4->close();

  $stmt2 = $mysqli->prepare("DELETE FROM decoydetails where decoyname=?");

  $stmt2->bind_param("s", $decoyname);

  $stmt2->execute();

  $stmt2->close();

  $stmt3 = $mysqli->prepare("DELETE FROM decoys where decoyname=?");

  $stmt3->bind_param("s", $decoyname);

  $stmt3->execute();

  $stmt3->close();

  

  //exec("sudo /bin/ps auxx| grep -i \"mainlog.sh\"| grep -i \"$interface\" |grep -i -v \"grep \"|awk -F \" \" '{print$2}'|xargs",$outputmainlogpid,$resultmainlogpid);
  //exec("sudo /bin/kill -9 $outputmainlogpid[0]",$outputmainlogkill,$resultmainlogkill);
  //exec("sudo /usr/bin/nohup /bin/sh mainlog.sh \"enp0s8\" > /dev/null 2>&1  &",$outputmainlog,$resultmainlog);


  echo "<script>
  alert('Decoy has been deleted.');
  window.location.href='list-decoys.php';
  </script>";
  exit();

  }
?>
  <!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Manage Decoy 
        <small>Manage</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Modals</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="callout callout-info">
        <h4>Warning!</h4>
        You are about to delete a Decoy.
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Choose Action</h3>
            </div>
            <div class="box-body">
              <!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-info"> -->
                 <!-- Edit Decoy --> 
              <!-- </button> -->
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-danger">
                Delete Decoy
              </button>
            </div>
          </div>
        </div>
      </div>

        <div class="modal modal-info fade" id="modal-info">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Info Modal</h4>
              </div>
              <div class="modal-body">
                <p>One fine body&hellip;</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline">Save changes</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal modal-danger fade" id="modal-danger">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>



		  <?php
		      $decoyname=$_GET["decoyname"];
                      echo '<h4 class="modal-title">Warning Message!!!</h4>';
		      echo '</div>
              			<div class="modal-body">
                		<p>Are you sure you want to delete "'.dataFilter($decoyname).'" decoy?&hellip;</p>
              			</div>
              			<div class="modal-footer">
                		<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                		<button type="button" class="btn btn-outline" onclick=document.getElementById("deletedecoy").submit();>Confirm</button>
              		   </div>';
		      echo '<form id="deletedecoy" action="manage-decoy.php" method="post">
				<input type="hidden" name="decoyname" value="'.dataFilter($decoyname).'"/>
				<input type="hidden" name="action" value="delete"/>
			   </form>';
                ?>

            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
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
