  <header class="main-header">
    <!-- Logo -->
    <a href="search.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>V</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>DejaVu</b> | Console<span style="font-size: 12px;"></span></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu <?php if(activeAlerts() > 0) 
            echo "alert-danger";
            ?>">
            <a href="events.php" >
            
              <i class="fa fa-warning"></i>
                <span class="hidden-xs">
                  Active Attacks (<?php echo activeAlerts();?>)
                </span>
            </li>
            </a>
              <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">

              <a href="logout.php" >
                
                <i class="fa fa-fw fa-sign-out"></i>
                <span class="hidden-xs">Sign Out</span>
              </a>

            </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>
