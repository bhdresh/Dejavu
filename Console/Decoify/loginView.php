<!-- Header.php. Contains header content -->
<?php 

//Access-Control-Allow-Origin header with wildcard.
header("Access-Control-Allow-Origin: *");

include 'template/header.php';

?>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>DejaVu</b> | Console
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
    if(isset($_GET["email"]) && $_GET["email"] == 'verified')
    {
    ?>
      <p class="text-red">Email verified! You can now login to Mirage Cloud.</p>
    <?php
    }
    if(isset($_GET["email"]) && $_GET["email"] == 'incorrect')
    {
    ?>
      <p class="text-red">Something seems wrong</p>
    <?php
    }
    if(isset($_GET["email"]) && $_GET["email"] == 'sent')
    {
    ?>
      <p class="text-red">You should have received an email. Please click on the activation link to verify and login.</p>
    <?php
    }
    if(isset($_GET["email"]) && $_GET["email"] == 'register')
    {
    ?>
      <p class="text-red">User registered! Please login.</p>
    <?php
    }
    if(isset($_GET["email"]) && $_GET["email"] == 'forgotEmailSent')
    {
    ?>
      <p class="text-red">You should have received an email with password reset link.</p>
    <?php
    }
    if(isset($_GET["password"]) && $_GET["password"] == 'updated')
    {
    ?>
      <p class="text-red">Password updated. You can now login with the new password.</p>
    <?php
    }
    if(isset($_GET["password"]) && $_GET["password"] == 'notexists')
    {
    ?>
      <p class="text-red">Oops...Looks like link expired</p>
    <?php
    }
    ?>




    <form action="login.php" method="post">
      <div class="form-group has-feedback">
        <input type="username" name="username" class="form-control" placeholder="Username" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <!--
      <div class="form-group has-feedback">
        <input type="OTP" name="OTP" class="form-control" placeholder="OTP">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      -->
      <div class="row">
        <div class="col-xs-8">
         
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-info">Sign In</button>
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
