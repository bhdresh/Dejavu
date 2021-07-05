<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
  <b>DejaVu </b>| Console 
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Register to get started</p>
    <?php if(isset($_GET["email"]) && $_GET["email"] == 'exists')
    {
    ?>
      <p class="text-red">Email already exists! Try to Sign In or Reset Password</p>
    <?php
    }
    ?>



    <form action="register.php" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="email" class="form-control" placeholder="Username" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="form-group has-feedback">
        <input type="text" name="org" class="form-control" placeholder="Organisation Name (Optional)">
        <span class="glyphicon glyphicon-globe form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
        </div>
        <!-- /.col -->
       
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-flat">Get Started</button>
        </div>
        <div class="social-auth-links text-center">
  </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <br />
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>
</html>
