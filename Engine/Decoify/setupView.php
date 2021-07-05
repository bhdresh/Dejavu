<?php 

include 'template/header.php';
include 'db.php';

  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT Username, Password, Status, Role  FROM Users where Status=1;");
  
  $stmt->execute();

  $result = $stmt->get_result();
  
  if($result->num_rows === 0) {
    
    $stmt->close();

    $user = 'no user';

  }

  else
  {
    $user = 'present user';
  }

  if ($user == 'present user')
  {

    header("Location: loginView.php"); 

  }
?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>DejaVu | Engine </b> Initial Setup
  </div>
  
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"><b>User Information</b></p>
    <form action="setup.php" method="post">
      <div class="form-group has-feedback">
        <input type="username" name="username" class="form-control" placeholder="Username" required>
        <span class="glyphicon fa fa-user-secret form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="hidden" name="secret-key" class="form-control" value="123123123" placeholder="MiragEye-API-Key" required>
        <!--<span class="fa fa-key form-control-feedback"></span>-->
      </div>

      <div class="row">
        <div class="col-xs-8">
         
        </div>
        <!-- /.col -->
        <!-- /.col -->
      </div>


    <div style="display: none;" id="mgmt_int">
    <p class="login-box-msg"><b>Management Interface (vboxnet0/eth0)</b></p>

    <?php

                  exec("sudo /sbin/ifconfig eth0| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
                  exec("sudo /sbin/ifconfig eth0| grep -i inet| grep -i netmask|awk -F \" \" '{print$4}'|grep [0-9]",$output4,$result);
                  exec("sudo /sbin/route -n| grep -i eth0|awk -F \" \" '{print$2}'|grep -v \"0.0.0.0\"|grep [0-9]",$output5,$result);

                  $ip = $output1[0];
                  $mask = $output4[0];
                  $gateway = $output5[0];
 

  ?>
      <div class="form-group has-feedback">
        <input type="text" name="ipad" class="form-control" placeholder="IP Address" value="<?=$ip; ?>" required>
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="text" name="mask" class="form-control" placeholder="Subnet Mask" value="<?=$mask; ?>" required>
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="text" name="gateway" class="form-control" placeholder="Gateway" value="<?=$gateway; ?>" required>
        <span class="glyphicon fa fa-laptop form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
         
        </div>
      </div>
    </div>
      <div class="row">
        <div class="col-xs-8">
         <button type="button" class="btn btn-block btn-info btn-xs" id="config_button">Configure Management Interface </button>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          
          <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
        </div>
        <!-- /.col -->
      </div></br>
      <font size="3" color="red">NOTE: System will reboot to reflect the changes.</font>
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>
<script type="text/javascript">
$(document).ready(function(){
   
   $("#config_button").click(function(){
   $("#mgmt_int").toggle();
   });
});
</script>
</html>
