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
<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>

<script>

function searchSubmit() {
  
  if($("#Email").val() == '') {
      alert("Please Enter Email");
      return false;
  }
  if($("#Username").val() == '') {
      alert("Please Enter Username");
      return false;
  }
  if($("#Pass").val() == '') {
      alert("Please Enter Password");
      return false;
  }
  if($("#Pass1").val() == '') {
      alert("Please Enter Confirm Password");
      return false;
  }
  if($("#Pass1").val() != $("#Pass").val()) {
      alert("Password and Confirm Password not match");
      return false;
  }

$("#formSearch").submit();

}

function modifyUser(alertID)
{

  var alertID = alertID;
  if($("#Email"+alertID).val() == '') {
      alert("Please Enter Email");
      return false;
  }
  if($("#Username"+alertID).val() == '') {
      alert("Please Enter Username");
      return false;
  }

   

    $("#formSearch" + alertID).submit();
}

function disbaleUser(alertID)
{
  var alertID = alertID;

  var action = 'disable';

  $("#disableAlert" + alertID).submit();

}


  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    })
  })
</script>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Manage Users
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-default">
        New User
        </button>
      </h1>
    </section>

    <!-- Main content -->
<section class="content">
      <div class="row">

        <div class="col-xs-12">
           <div class="modal fade in" id="modal-default" style="display: none;">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">Ã—</span></button>
                    <h4 class="modal-title">Create New User</h4>
                  </div>
                  <div class="modal-body">

                    <form id="formSearch" name="formSearch" action="manageUsers.php" method="POST">

                      <input type="hidden" name="jsonSearchString" id="jsonSearchString" value="" />
                      <input type="hidden" name="action"" value="add" />

                      <div class="form-group">
                        <label for="exampleInputEmail1">Email Address</label>
                        <input type="email" class="form-control" name="Email" id="Email" placeholder="Enter Email Address">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="email" class="form-control" name="Username" id="Username" placeholder="Enter Username">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Password</label>
                        <input type="password" class="form-control" name="Pass" id="Pass" placeholder="Enter Password">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Confirm Password</label>
                        <input type="password" class="form-control" name="Pass1" id="Pass1" placeholder="Enter Confirm Password">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Role</label>
                        <div class="margin" id="advanceSearch">
                        <select name="Role" id="Role">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            
                          </select>    
                      </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="exampleInputEmail1">Status</label>
                        <div class="margin" id="advanceSearch">
                        <select name="Status" id="Status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                            
                          </select>    
</div>
                      </div>
  
                    </form>
                                           
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="advSearchSubmit" onclick="searchSubmit()">Add User</button>
                  </div>
                </div>
                <!-- /.modal-content -->
              </div>
              <!-- /.modal-dialog -->
           </div>
          
          <div class="col-xs-12">
          <div class="box">
            <div class="box-header">

            </div>
            <!-- /.box-header -->
                  <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                      <tr>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Modify</th>
                        <th>Delete</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($event as $key => $value): ?>
                      <tr role="row" class="odd">
                        <td class=""><?= dataFilter($event[$key]['Email'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Username'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Role'])?></td>
                        <td class=""><?= dataFilter($event[$key]['Status'])?></td>
                        <td class="" style="text-align: center;">
                        <?php 
                           $alert_id = $event[$key]['ID'];
                        ?>
                          <button type="button" data-toggle="modal" data-target="#modal-default<?= $alert_id?>" >   <span class="glyphicon glyphicon-edit"></span>
                          </button>
                        </td>
                        <div class="modal fade in" id="modal-default<?= $alert_id?>" style="display: none;">
                              <div class="modal-dialog">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">Ã—</span></button>
                                    <h4 class="modal-title">Modify User</h4>
                                  </div>
                                  <div class="modal-body">

                                    <form id="formSearch<?= $alert_id?>" name="formSearch" action="manageUsers.php" method="POST">

                                      <input type="hidden" name="jsonSearchString" id="jsonSearchString<?= $alert_id?>" value="" />

                                      <input type="hidden" name="alert_id"" value="<?= $alert_id?>" />
                                      <input type="hidden" name="action"" value="modify" />


                                      <div class="form-group">
                        <label for="exampleInputEmail1">Email Address</label>
                        <input type="text" class="form-control" name="Email" id="Email<?= $alert_id?>" placeholder="Enter Email Address" value="<?= dataFilter($event[$key]['Email'])?>">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" name="Username" id="Username<?= $alert_id?>" placeholder="Enter Username" value="<?= dataFilter($event[$key]['Username'])?>">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Password</label>
                        <input type="password" class="form-control" name="Pass" id="Pass" placeholder="Enter Password">
                      </div>

                      <div class="form-group">
                        <label for="exampleInputEmail1">Role</label>
                        <?php
                          $admin = '';
                          $user = '';
                          if($event[$key]['Role'] == 'Admin') {
                              $admin = "selected='selected'";
                          }
                          else {
                            $user = "selected='selected'";
                          }
                        ?>
                        <div class="margin" id="advanceSearch">
                        <select name="Role" id="Role">
                            <option value="admin" <?php echo $admin; ?>>Admin</option>
                            <option value="user" <?php echo $user; ?>>User</option>
                            
                          </select>    </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="exampleInputEmail1">Status</label>
                        <?php
                          $active = '';
                          $inactive = '';
                          if($event[$key]['Status'] == 'Active') {
                              $active = "selected='selected'";
                          }
                          else {
                            $inactive = "selected='selected'";
                          }
                        ?>
                        <div class="margin" id="advanceSearch">
                        <select name="Status" id="Status">
                            <option value="1" <?php echo $active; ?>>Active</option>
                            <option value="0" <?php echo $inactive; ?>>Inactive</option>
                            
                          </select>    </div>
                      </div>

                        </form>
                               
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="modifyUser(<?= $alert_id?>)">Modify User</button>
                                  </div>
                                </div>
                                <!-- /.modal-content -->
                              </div>
                              <!-- /.modal-dialog -->
                        </div>

                        <form id="disableAlert<?= $alert_id?>" action="manageUsers.php" method="POST">

                                      <input type="hidden" name="alert_id"" value="<?= $alert_id?>" />
                                      <input type="hidden" name="action"" value="disable" />
                        </form>
                        <td class="" style="text-align: center;"><span class="glyphicon glyphicon-remove" onclick="disbaleUser(<?= $alert_id?>)"></span></td>
                      </tr>
                      <?php endforeach;?>
                      </tbody>
                    </table>
                  </div>
              </div>
            </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<!-- DataTables -->
<script src="UIElements/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="UIElements/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
 <!-- DataTables -->
<link rel="stylesheet" href="UIElements/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
<?php include 'template/main-footer.php';?>

</body>
<?php
}
else 
{
  header('location:loginView.php');
}
?>

