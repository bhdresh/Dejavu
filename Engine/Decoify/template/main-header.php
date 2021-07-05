  <header class="main-header">
    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>V</b>u</span>
      <!-- logo for regular state and mobile devices -->
     <span class="logo-lg"><b>DejaVu | </b>Engine<span style="font-size: 12px;"></span></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <script language="javascript"> 
            function DoPost(){
              $(document).ready(function(){
                $("#cloudLogin").submit();
              });
            }
          </script>

<script>
$(document).ready(function(){ 
<?php 
if(GetApiStatus() == 1){
?>
  $.ajax({url: "updateSettings.php", data: "check=api&csrf_token=<?php echo $_SESSION['csrf_token']; ?>", success: function(result){
  var api_value;
  api_value = result;
  if(api_value == "<p class=\"text-red\">Connection to DejaVu seems to be okay. However, the API key is not a valid one. Update your API key below and try again!</p>" || api_value == "<p class=\"text-red\">API Connection Failed! Check API KEY and ensure connection to DejaVu Console!</p>"){
    
    $('#cloud_fail').css("display", "block");
  }
  else{
    $('#cloud_success').css("display", "block");
  }  
}});
<?php
}
?>
  $.ajax({url: "getCount.php", data: "count=eventCount", success: function(result2){
      var api_value2;
      api_value2 = result2;
      $("#active_event_count").text(api_value2);
      var int_count = parseInt(api_value2);
      if(int_count > 0){
        $("#event_count").addClass( "alert-danger" );
      }
    }});
});
</script> 

<div class="modal modal-danger fade in" id="api-modal-warning" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">DejaVu Console API Connection Failed</h4>
      </div>
      <div class="modal-body">
        <p>Connection to DejaVu Console cannot be established. Logs cannot be viewed on the dashboard. <br/>
It is recommended to enable outbound connection to DejaVu Console"</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="location.href='updateSettingsView.php'">Check Connection Again</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<!-- API CAll Check End -->
          <li id="cloud_success" style="display: none;">
            <a href="cloudSettings.php#api-connection-check">
            <i class="fa fa-circle text-green"></i>
              <span class="hidden-xs">Console Connection</span>
            </a>
          </li>
          <li id="cloud_fail" style="display: none;">
            <a href="cloudSettings.php#api-connection-check">
            <i class="fa fa-circle text-red"></i>
              <span class="hidden-xs">Console Connection</span>
            </a>
          </li>
          <li id="event_count">
            <a href="javascript:DoPost()">
              <i class="fa fa-fw fa-gears"></i>
              <span class="hidden-xs">DejaVu Console (<div style="display:inline;" id="active_event_count"></div>)</span>
            </a>
          </li>
          <li class="dropdown user user-menu">

            <a href="logout.php" >
              <span class="hidden-xs">Sign Out</span>
            </a>

          <form action="https://<?= GetConsole_IP()?>/Decoify/login.php" method="post" id="cloudLogin" target="_blank">
              <input type="hidden" name="api_key" value="<?php if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') { echo GetApiKey();} ?>" >
          </form>

          </li>
          <!-- Control Sidebar Toggle Button -->
          
        </ul>
      </div>
    </nav>
  </header>

