<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="UIElements/dist/img/sidebar-user1.png" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['user_name']; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <script type="text/javascript">
        jQuery(document).ready(function($){
          // Get current path and find target link
          var path = window.location.pathname.split("/").pop();
          
          // select side menu based on page
          if ( path == 'list-decoys.php' || path == 'add-server-decoys.php' || path == 'add-client-decoys.php') {
            var target = $('.mainnav');
            // Add active class to target link
            target.addClass('active');
          }

          if ( path == 'add-vlans.php' || path == 'del-vlans.php' || path == 'addFiles.php') {
            var target = $('.nwmenu');
            // Add active class to target link
            target.addClass('active');
          }

          if ( path == 'search.php' || path == 'loggraph.php' || path == 'manageAlerts.php' || path == 'events.php' || path == 'dashboard.php') {
            var target = $('.logmenu');
            // Add active class to target link
            target.addClass('active');
          }
          

          if ( path == 'crumbDecoy.php' || path == 'crumbHash.php' || path == 'crumbKerb.php') {
            var target = $('.breadmenu');
            // Add active class to target link
            target.addClass('active');
          }

          if ( path == 'deviceSettings.php' || path == 'manageUsers.php' || path == "backupSettings.php") {
            var target = $('.settings');
            // Add active class to target link
            target.addClass('active');
          }

        });
      </script>
	  <?php 
		$file = basename($_SERVER['SCRIPT_FILENAME']);
	  ?>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview mainnav">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Key Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="list-key.php"><i class="fa fa-circle-o"></i> List Key</a></li>
          </ul>
        </li>


        <li class="treeview logmenu">
          <a href="#">
            <i class="fa fa-laptop"></i> <span>Threat Analysis</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="events.php"><i class="fa fa-circle-o"></i> Active Attacks</a></li>
            <li><a href="dashboard.php"><i class="fa fa-circle-o"></i> Attack Graph</a></li>
            <!-- <li><a href="loggraph.php"><i class="fa fa-circle-o"></i> Event Dashboard </a></li> -->
            <li><a href="search.php"><i class="fa fa-circle-o"></i> Raw Logs</a></li>
            <li><a href="manageAlerts.php"><i class="fa fa-circle-o"></i> Manage Notifications</a></li>
          </ul>
        </li>

        <li class="treeview settings">
          <a href="#">
            <i class="fa fa-gear"></i> <span>Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li <?php echo $file==="deviceSettings.php"?"class='active'":''; ?>><a href="deviceSettings.php"><i class="fa fa-circle-o"></i> Device Settings</a></li>
            <li <?php echo $file==="manageUsers.php"?"class='active'":''; ?>><a href="manageUsers.php"><i class="fa fa-circle-o"></i> User Management</a></li>
            <li <?php echo $file==="backupSettings.php"?"class='active'":''; ?>><a href="backupSettings.php"><i class="fa fa-circle-o"></i> Backup & Upgrage</a></li>
          </ul>
        </li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
