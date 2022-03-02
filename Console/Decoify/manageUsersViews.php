<?php
if (!isset($_SESSION)) {
  session_start();
}
require_once('includes/common.php');

if (isset($_SESSION['user_name']) && isAdmin($_SESSION)) {
?>
  <!-- Header.php. Contains header content -->
  <?php include 'template/header.php'; ?>

  <body class="hold-transition skin-black-light sidebar-mini">
    <?php include 'template/main-header.php'; ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php include 'template/main-sidebar.php'; ?>
    <script>
      $(document).on('change', '#condition', function() {
        $(this).closest('div').find(".search_data").show();
        $(this).closest('div').find(".search_data").val('');
      });

      function Remove(obj) {
        $(obj).closest('div').remove();
      }

      function searchSubmit() {
        var masterUser = [];

          var username = $('#username').val();
          var role = $('#role').val();
          var email = $('#email').val();
          var status = $('#status').val();
          var password = $('#password').val();
          var cpassword = $('#cpassword').val();

          if(username.trim()===""){
            $('div#usernameb').addClass("has-warning");
            return false;
          } else {
            $('div#usernameb').removeClass("has-warning");
          }

          if(role.trim()===""){
            $('div#roleb').addClass("has-warning");
            return false;
          } else {
            $('div#roleb').removeClass("has-warning");
          }

          if(email.trim()===""){
            $('div#emailb').addClass("has-warning");
            return false;
          } else {
            $('div#emailb').removeClass("has-warning");
          }

          if(status.trim()===""){
            $('div#statusb').addClass("has-warning");
            return false;
          } else {
            $('div#statusb').removeClass("has-warning");
          }
          
          if(password.trim()!==cpassword.trim()){
            $('div#passwordb').addClass("has-warning");
            $('div#cpasswordb').addClass("has-warning");
            return false;
          } else {
            $('div#passwordb').removeClass("has-warning");
            $('div#cpasswordb').removeClass("has-warning");
          }

          item = {};

          item["username"] = username;
          item["role"] = role;
          item["email"] = email;
          item["status"] = status;
          item["password"] = password;
          item["cpassword"] = cpassword;

          masterUser.push(item);

        var action = 'add';

        var jsonSearchString = JSON.stringify(masterUser);

        $('#jsonSearchString').val(jsonSearchString);

        $("#formSearch").submit();

      }

      function modifyUser(userID) {
        var userID = userID;
        var masterUser = [];


          var username = $('#username' + userID).val();
          var role = $('#role' + userID).val();
          var email = $('#email' + userID).val();
          var status = $('#status' + userID).val();

          if(username.trim()===""){
            $('div#usernamed').addClass("has-warning");
            return false;
          } else {
            $('div#usernamed').removeClass("has-warning");
          }

          if(role.trim()===""){
            $('div#roled').addClass("has-warning");
            return false;
          } else {
            $('div#roled').removeClass("has-warning");
          }

          if(email.trim()===""){
            $('div#emaild').addClass("has-warning");
            return false;
          } else {
            $('div#emaild').removeClass("has-warning");
          }

          if(status.trim()===""){
            $('div#statusd').addClass("has-warning");
            return false;
          } else {
            $('div#statusd').removeClass("has-warning");
          }

          item = {};

          item["username"] = username;
          item["role"] = role;
          item["email"] = email;
          item["status"] = status;

          masterUser.push(item);

        var action = 'modify';

        var jsonSearchString = JSON.stringify(masterUser);

        $('#jsonSearchString' + userID).val(jsonSearchString);

        $("#formEdit" + userID).submit();
      }

      function disbaleUser(userID,status) {
        var userID = userID;
        $('#user_status' + userID).val(status);
        $("#disableUser" + userID).submit();
      }
    </script>

    <script>
      $(function() {
        $('#example1').DataTable()
        $('#example2').DataTable({
          'paging': true,
          'lengthChange': false,
          'searching': false,
          'ordering': true,
          'info': true,
          'autoWidth': true
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
                      <span aria-hidden="true">X</span></button>
                    <h4 class="modal-title">Create New User</h4>
                  </div>
                  <div class="modal-body">
                    <!--add form start-->
                    <form id="formSearch" name="formSearch" action="manageUsers.php" method="POST">

                      <input type="hidden" name="jsonSearchString" id="jsonSearchString" value="" />
                      <input type="hidden" name="action"" value="add" />

                      <div class="form-group" id="usernameb">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username">
                      </div>

                      <div class="form-group" id="roleb">
                        <label for="email">Role</label>
                        <select id="role" name="role" class="form-control">
                          <option value="">Please select role</option>
                          <option value="admin">Admin</option>
                          <option value="user">User</option>
                        </select>
                      </div>

                      <div class="form-group" id="emailb">
                        <label for="email">Email address</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
                      </div>

                      <div class="form-group" id="statusb">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                          <option value="">Please select status</option>
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>

                      <div class="form-group" id="passwordb">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                      </div>

                      <div class="form-group" id="cpasswordb">
                        <label for="cpassword">Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Confirm Password">
                      </div>
                    </form>
                    <!--add form end-->
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="searchSubmit()">Add User</button>
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
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Modify</th>
                        <th>Delete</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($event as $key => $value) : ?>
                        <tr role="row" class="odd">
                          <td class=""><?php echo dataFilter($event[$key]['Username']) ?></td>
                          <td class=""><?php echo dataFilter($event[$key]['Email']) ?></td>
                          <td class=""><?php echo dataFilter($event[$key]['Role']) ?></td>
                          <td class=""><?php echo dataFilter($event[$key]['Status'])==="1"?"Active":"Inactive"; ?></td>
                          <td class="" style="text-align: center;">
                            <?php
                            $user_info = $event[$key];
                            $user_id = $user_info['ID'];
                            ?>
                            <button type="button" data-toggle="modal" data-target="#modal-default<?php echo $user_id; ?>"> <span class="glyphicon glyphicon-edit"></span>
                            </button>
                          </td>
                          <div class="modal fade in" id="modal-default<?php echo  $user_id ?>" style="display: none;">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">X</span></button>
                                  <h4 class="modal-title">Modify User</h4>
                                </div>
                                <div class="modal-body">

                                  <form id="formEdit<?php echo $user_id; ?>" name="formEdit" action="manageUsers.php" method="POST">

                                    <input type="hidden" name="jsonSearchString" id="jsonSearchString<?php echo $user_id; ?>" value="" />

                                    <input type="hidden" name="user_id" value=" <?php echo $user_id; ?>" />
                              <input type="hidden" name="action" value="modify" />

                                    <div class="form-group" id="usernamed">
                                      <label for="username">Username</label>
                                      <input type="email" class="form-control" name="username" id="username<?php echo $user_id; ?>" value="<?= dataFilter($event[$key]['Username']) ?>">
                                    </div>

                                    <div class="form-group" id="roled">
                                      <label for="role">Role</label>
                                      <select id="role<?php echo $user_id; ?>" name="role" class="form-control">
                                        <option value="">Please select role</option>
                                        <option <?php echo dataFilter($event[$key]['Role']) == "admin" ? "selected" : ""; ?> value="admin">Admin</option>
                                        <option <?php echo dataFilter($event[$key]['Role']) == "user" ? "selected" : ""; ?>  value="user">User</option>
                                      </select>
                                    </div>

                                    <div class="form-group" id="emaild">
                                      <label for="email">Email</label>
                                      <input type="email" class="form-control" name="email" id="email<?php echo $user_id; ?>" value="<?= dataFilter($event[$key]['Email']) ?>">
                                    </div>
									<?php
										$sameuser = ($_SESSION['user_name'] == dataFilter($event[$key]['Username']))?"style='display:none;'":'';
										
									?>

                                    <div class="form-group" id="statusd"  <?php echo $sameuser;?>>
                                      <label for="status">Status</label>
                                      <select id="status<?php echo $user_id; ?>" name="status" class="form-control" >
                                        <option value="">Please select status</option>
                                        <option <?php echo dataFilter($event[$key]['Status']) == "1" ? "selected" : ""; ?> value="1">Active</option>
                                        <option <?php echo dataFilter($event[$key]['Status']) == "0" ? "selected" : ""; ?>  value="0">Inactive</option>
                                      </select>
                                    </div>
									

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="advSearchSubmit" onclick="modifyUser(<?php echo $user_id ?>)">Modify User</button>
                                  </div>
								</form>

                                </div>
                                <!-- /.modal-content -->
                              </div>
                              <!-- /.modal-dialog -->
                            </div>

                            <form id="disableUser<?php echo $user_id ?>" action="manageUsers.php" method="POST">

                              <input type="hidden" name="user_id" value="<?php echo $user_id;?>" />
                              <input type="hidden" name="action" value="statuschange" />
                              <input type="hidden" id="user_status<?php echo $user_id;?>" name="user_status" value="<?php echo $user_id;?>" />
                            </form>
                            <td class="" style="text-align: center;">
                            <?php						
							if($_SESSION['user_name']!=dataFilter($event[$key]['Username'])) { ?>
                            <span class="glyphicon glyphicon-remove" onclick="disbaleUser(<?php echo $user_id; ?>,'0')"></span>
                          
                            <span class="glyphicon glyphicon-ok" onclick="disbaleUser(<?php echo $user_id; ?>,'1')"></span>
							
							<?php } else {
								echo "-";
							}
							?>
                          
                          </td>
                        </tr>
                      <?php endforeach; ?>
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
    <?php include 'template/main-footer.php'; ?>

  </body>
<?php
} else {
  header('location:loginView.php');
}
?>