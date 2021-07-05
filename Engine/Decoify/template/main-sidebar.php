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
          <p>Admin</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
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

          if ( path == 'search.php' || path == 'loggraph.php' || path == 'manageAlerts.php' || path == 'events.php') {
            var target = $('.logmenu');
            // Add active class to target link
            target.addClass('active');
          }

          if ( path == 'crumbDecoy.php' || path == 'crumbHash.php' || path == 'crumbKerb.php') {
            var target = $('.breadmenu');
            // Add active class to target link
            target.addClass('active');
          }

          if ( path == 'deviceSettings.php' || path == 'cloudSettings.php' || path == 'backupSettings.php') {
            var target = $('.settings');
            // Add active class to target link
            target.addClass('active');
          }

        });
      </script>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview mainnav">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Decoy Management</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="list-decoys.php"><i class="fa fa-circle-o"></i> Manage Decoys</a></li>
            <li><a href="add-server-decoys.php"><i class="fa fa-circle-o"></i> Add Server Decoy</a></li>
            <li><a href="add-client-decoys.php"><i class="fa fa-circle-o"></i> Add Client Decoy</a></li>
            
          </ul>
        </li>

        <li class="treeview nwmenu">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>N/W and File Managment</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="add-vlans.php"><i class="fa fa-circle-o"></i> Add Vlan</a></li>
            <li><a href="del-vlans.php"><i class="fa fa-circle-o"></i> Delete Vlan</a></li>
            <li><a href="addFiles.php"><i class="fa fa-circle-o"></i> Manage File Structure</a></li>
          </ul>
        </li>

        <li class="treeview breadmenu">
          <a href="#">
            <i class="fa fa-th"></i> <span>Breadbcrumbs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="crumbDecoy.php"><i class="fa fa-circle-o"></i> Add Decoy to Domain</a></li>
            <li><a href="crumbHash.php"><i class="fa fa-circle-o"></i> Create HoneyHash</a></li>
            <li><a href="crumbKerb.php"><i class="fa fa-circle-o"></i> Kerberoast HoneyAccount</a></li>
	          <li><a href="honeyfiles.php"><i class="fa fa-circle-o"></i> HoneyFiles</a></li> 
          </ul>
        </li>
        <li class="treeview settings">
          <a href="updateSettings.php">
            <i class="fa fa-microchip"></i>
            <span>Settings</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="deviceSettings.php"><i class="fa fa-circle-o"></i> Device Settings</a></li>
            <li><a href="cloudSettings.php"><i class="fa fa-circle-o"></i> Connection & Logging</a></li>
            <li><a href="backupSettings.php"><i class="fa fa-circle-o"></i> Backup & Upgrade</a></li>
          </ul>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
