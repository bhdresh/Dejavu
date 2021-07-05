<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include 'db.php';

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

$user_id=$_SESSION['user_id'];  

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
        Key Management
        <small>Key List</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Key Management</a></li>
        <li class="active">Auth key</li>
      </ol>
    </section>
	<script>
		function conftotpreset(form) {
			if (confirm("Are you sure you want to reset the TOTP secret?")) {
				resettotpkey();
			}

		}
	</script>


	<script>
		function confauthreset(form) {
			if (confirm("Are you sure you want to reset the Auth key?")) {
				resetauthkey();
			}

		}
	</script>



     <script>
	function resetauthkey() {
         document.getElementById("resetauthkey").submit();
      	}
      </script>


      <form id="resetauthkey" action="reset-key.php" method="post">
       <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
       <input type="hidden" name="action" value="resetauthkey"/>
      </form>


      <script>
		function resettotpkey() {
        	document.getElementById("resettotpkey").submit();
      		}
      </script>

      <form id="resettotpkey" action="reset-key.php" method="post">
       <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
       <input type="hidden" name="action" value="resettotpkey"/>
      </form>


    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Name</th>
		  <th>Value</th>
		  <th>Update Date</th>		
		  <th class="" style="text-align: center;">Reset key</th>
                </tr>
                </thead>
                <tbody>


                <?php

                    $mysqli = db_connect();
                    $stmt = $mysqli->prepare("SELECT * FROM Users where user_id=?;");
		    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                      if (mysqli_num_rows($result) > 0) {
                          // output data of each row
                          while($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '  <td>Auth Key</td>';
				echo '	<td>'.dataFilter($row["auth_key"]).'</td>';
				echo '  <td>'.dataFilter($row["auth_key_timestamp"]).'</td>';
			 	echo ' <td class="" style="text-align: center;"><span class="glyphicon glyphicon-refresh" onclick="confauthreset(this.form);"></span></td>';
				echo '</tr>';

                          }
                      } else {
                          echo "0 results";
                      }

                      $stmt->close();
                ?> 

                </tbody>
                <tfoot>
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
