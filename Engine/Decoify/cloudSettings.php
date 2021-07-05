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
            Dashboard Connection & Logging
      </h1>
      <?php if(isset($_GET["config"]) && $_GET["config"] == 'success')
          {
          ?>
            <p class="text-red">Previous configuration restore successful</p>
            <p class="text-red">Reboot to apply changes. Please note the updated Management Interface : <?= $_GET["interface"];?> </p>
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
        <li class="active">Dashboard Connection & Logging</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    

          <div class="box box-primary" id="api-connection-check">
            <div class="box-header with-border">
              <h3 class="box-title">API Connection Check</h3>
              <img src="template/loader.gif" id="loader-img" style="display:none; height:40px; width:40 px"/ >
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
                  <script>
                      $(document).ready(function(){
                        <?php 
                        if(GetApiStatus() == 1){
                        ?>
                        $('#loader-img').show(); 
                        $.ajax({url: "updateSettings.php", data: "check=api&csrf_token=<?php echo $_SESSION['csrf_token']; ?>", success: function(result){
                                $("#api-result").html(result);
                                $('#loader-img').hide(); 
                        }});
                        <?php
                        }
                        ?>
                        
                        $("#test-connection").click(function(){
                            $('#loader-img').show(); 
                            $.ajax({url: "updateSettings.php", data: "check=api&csrf_token=<?php echo $_SESSION['csrf_token']; ?>", success: function(result){
                              $("#api-result").html(result);
                              $('#loader-img').hide(); 
                          }});
                        });
                      });
                  </script>
                  <b class="text-green" id="api-result"></b>
                  <br/>
                  <button type="submit" value="Yes" id="test-connection" class="btn btn-primary">Test Connection</button>
            </div>   

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update API Key</h3>
              
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <?php
              if(isset($_GET["key"]) && $_GET["key"] == 'IPsuccess')
              {
              ?>
              <b class="text-blue">API Key Updated</b>
              <?php
              }
              ?>
              <form role="form" action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
            
              <div class="form-group has-feedback" style="width: 300px">
                <input type="username" name="secret-key" class="form-control" placeholder="DejaVu Console API Key" required>
                <span class="fa fa-key form-control-feedback"></span>
              </div>
              <br>
      
              <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Update</button>
                </div>
              </form>
              </div>    


              <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update DejaVu Console IP</h3>
              
            </div>
            
            <!-- /.box-header -->
            <div class="box-body">
              <?php
              if(isset($_GET["key"]) && $_GET["key"] == 'IPsuccess')
              {
              ?>
              <b class="text-blue">DejaVu Console IP Updated</b>
              <?php
              }
              ?>
              <form role="form" action="updateSettings.php" method="post">
		          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
           
		<?php

		$mysqli = db_connect();

              $stmt = $mysqli->prepare("SELECT * FROM Users");

              $stmt->execute();

              $result = $stmt->get_result();

                    $row = mysqli_fetch_assoc($result);

              $stmt->close();
		?>
 
              <div class="form-group has-feedback" style="width: 300px">
                <input type="IP" name="Console_IP" class="form-control" placeholder="DejaVu Console IP" value="<?=$row['Console_IP'] ?>" required>
                <span class="fa fa-key form-control-feedback"></span>
              </div>
              <br>
      
              <button type="submit" name="addvlan" value="Yes" class="btn btn-primary">Update</button>
                </div>
              </form>
              </div>    

              <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Update Syslog Details</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="updateSettings.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
              <?php
                
                  //Get Syslog Info

              $mysqli = db_connect();
  
   
              $stmt = $mysqli->prepare("SELECT IP, PORT, Protocol FROM SyslogDetails where Status='1'");
  
              $stmt->execute();

              $result = $stmt->get_result();

	            $row = mysqli_fetch_assoc($result);

              $stmt->close();

              ?>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="syslog_server_ip" id="syslog_server_ip1" class="form-control" placeholder="Syslog Server IP (Optional)" value="<?=$row['IP'] ?>">
                <span class="glyphicon fa fa-laptop form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <input type="text" name="syslog_port" class="form-control" id="syslog_server_port1"  placeholder="Syslog Server Port (Optional)" value="<?=$row['PORT'] ?>">
               <span class="glyphicon fa fa-laptop form-control-feedback"></span></span>
              </div>
              <div class="form-group has-feedback" style="width: 300px">
                <select class="form-control" name="protocol">
                  <?php
                    if($row['Protocol'] == 'UDP')
                    {
                      echo "<option value=\"TCP\" name=\"sylog_service\">TCP</option>";
                      echo "<option value=\"UDP\" selected=\"selected\" name=\"sylog_service\">UDP</option>";
                    }
                    else{
                      echo "<option value=\"TCP\" selected=\"selected\" name=\"sylog_service\">TCP</option>";
                      echo "<option value=\"UDP\" name=\"sylog_service\">UDP</option>";
                    }
                  ?>
                </select>
              </div>
     	      <label>
              <?php

              //Get SyslogServer Info

                $mysqli = db_connect();
                $stmt = $mysqli->prepare("SELECT EnableAPI FROM Users Limit 1;");
                $stmt->execute();

                $result = $stmt->get_result();

                while($row = $result->fetch_assoc()) {
                  $event[] = $row;
                }

                $EnableAPI = $event[0]['EnableAPI'];

                if($EnableAPI == 1) {
                  echo "<input type=\"checkbox\" class=\"flat-red\" value=\"yes\" id=\"EnableApi\" name=\"EnableApi\" checked onchange=\"check_api(this)\">";
                } 
                
                else {
                  echo "<input type=\"checkbox\" class=\"flat-red\" value=\"yes\" id=\"EnableApi\" name=\"EnableApi\" onchange=\"check_api(this)\">";
                }
                ?>	
                 Send Logs to DejaVu Console
              </label>
		<br><br>
                <script type="text/javascript">
                    function check_api(obj) {
                      if($(obj).prop("checked") == false){
                        
                        var ip = $("#syslog_server_ip1").val();
                        var port = $("#syslog_server_port1").val();
                        
                        if((jQuery.trim(ip).length == 0) || (jQuery.trim(port).length == 0)){
                            alert("Enter value for Syslog to disable logs to MiragEye Console!");
                            $(obj).prop('checked', true);;
                        }

                      }
                    }
                </script>
              <button type="submit" name="addsyslog" value="Yes" class="btn btn-primary">Update</button>
                </div>
              </form>
              </div>




	

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
