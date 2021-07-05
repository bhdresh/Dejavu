  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version: </b>
      <?php 
      $config = parse_ini_file('config/config.ini'); 
      echo $config['currentVersion'];
      ?>
    </div>
    <strong>Copyright &copy; 2020 <a href="https://www.camolabs.io">CamoLabs</a>.</strong> All rights
    reserved.
  </footer>
