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


// Get session data
$sessData = !empty($_SESSION['sessData'])?$_SESSION['sessData']:'';

// Get status message from session
if(!empty($sessData['status']['msg'])){
    $statusMsg = $sessData['status']['msg'];
    $statusMsgType = $sessData['status']['type'];
    unset($_SESSION['sessData']['status']);
}

// Load pagination class
require_once 'Pagination.class.php';

// Load and initialize database class
require_once 'db.class.php';
$db = new DB();

// Page offset and limit
$perPageLimit = 5;
$offset = !empty($_GET['page'])?(($_GET['page']-1)*$perPageLimit):0;

// Get search keyword
$searchKeyword = !empty($_GET['sq'])?$_GET['sq']:'';
$searchStr = !empty($searchKeyword)?'?sq='.$searchKeyword:'';

// Search DB query
$searchArr = '';
if(!empty($searchKeyword)){
    $searchArr = array(
        'username' => $searchKeyword,
        'email' => $searchKeyword,
        'role' => $searchKeyword
    );
}

// Get count of the users
$con = array(
    'like_or' => $searchArr,
    'return_type' => 'count'
);
$rowCount = $db->getRows('users', $con);

// Initialize pagination class
$pagConfig = array(
    'baseURL' => 'index.php'.$searchStr,
    'totalRows' => $rowCount,
    'perPage' => $perPageLimit
);
$pagination = new Pagination($pagConfig);

// Get users from database
$con = array(
    'like_or' => $searchArr,
    'start' => $offset,
    'limit' => $perPageLimit,
    'order_by' => 'id DESC',
);
$users = $db->getRows('users', $con);

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
        User Management
      </h1>

        <!-- Display status message -->
        <?php if(!empty($statusMsg) && ($statusMsgType == 'success')){ ?>
        <div class="alert alert-success"><?php echo $statusMsg; ?></div>
        <?php }elseif(!empty($statusMsg) && ($statusMsgType == 'error')){ ?>
        <div class="alert alert-danger"><?php echo $statusMsg; ?></div>
        <?php } ?>

      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">User Management</li>
      </ol>
    </section>
   

    <!-- Main content -->
    <section class="content">


    <div class="row">
    <div class="col-xs-12">
    <div class="box">
            <div class="box-header">
        <!-- Search form -->
        <form>
        <div class="input-group">
            <input type="text" name="sq" class="form-control" placeholder="Search by keyword..." value="<?php echo $searchKeyword; ?>">
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit">
                    <i class="glyphicon glyphicon-search"></i>
                </button>
            </div>
        </div>
        </form>
        
        <!-- Add link -->
        <span class="pull-right">
            <a href="addEdit.php" class="btn btn-primary"><i class="glyphicon glyphicon-plus"></i> New User</a>
        </span>
            </div></div>

    <!-- Data list table --> 
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th></th>
                <th>UserName</th>
                <th></th>
                <th>Email</th>
                <th></th>
                <th>Status</th>
                <th></th>
                <th>Role</th>
                <th></th>
                <th>TimeZone</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($users)){ $count = 0; 
                foreach($users as $user){ $count++;
            ?>
            <tr>
                <td><?php echo $user['ID']; ?></td>
                <td><?php echo $user['Username']; ?></td>
                <td><?php echo $user['Email']; ?></td>
                <td><?php echo $user['Status']; ?></td>
                <td><?php echo $user['Role']; ?></td>
                <td><?php echo $user['Timezone']; ?></td>
                <td>
                    <a href="addEdit.php?id=<?php echo $user['ID']; ?>" class="glyphicon glyphicon-edit">Edit</a>
                    <a href="userAction.php?action_type=delete&id=<?php echo $user['ID']; ?>" class="glyphicon glyphicon-trash" onclick="return confirm('Are you sure to delete?')">Delete</a>
                </td>
            </tr>
            <?php } }else{ ?>
            <tr><td colspan="5">No user(s) found......</td></tr>
            <?php } ?>
        </tbody>
    </table>
    

    <!-- Display pagination links -->
    <?php echo $pagination->createLinks(); ?>



    </div>
    

</div>










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
                  
                    $ip = isset($output1[0])?$output1[0]:"";    
                    $mask = isset($output4[0])?$output4[0]:"";    
                    $gateway = isset($output5[0])?$output5[0]:""; 

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