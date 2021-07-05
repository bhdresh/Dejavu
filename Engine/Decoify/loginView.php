<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>DejaVu</b> | Engine
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in</p>
    <?php if(isset($_GET["pass"]) && $_GET["pass"] == 'fail')
    {
    ?>
      <p class="text-red">Invalid Username/Password</p>
    <?php
    }
    ?>
    <form action="login.php" method="post">
      <div class="form-group has-feedback">
        <input type="username" name="username" class="form-control" placeholder="Username">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
         
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>
</html>
