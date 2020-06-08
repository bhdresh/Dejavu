<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}


if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {


if (isset($_POST['generatePS']))
{
if ($_POST['domainname'] == true)
{
$domainname=$_POST["domainname"];

$myfile = fopen("/var/log/data/Decoycrumb.ps1", "wr") or die("Unable to open file!");
$txt = "Import-Module ActiveDirectory\n";
fwrite($myfile, $txt);
fclose($myfile);

foreach ($_POST as $key => $value) {

if($value == 'Y')
{
$decoyname=htmlspecialchars($key);

$mysqli = db_connect();
$stmt = $mysqli->prepare("SELECT * FROM decoys WHERE decoyname=?");
$stmt->bind_param("s", $decoyname);
$stmt->execute();
$result = $stmt->get_result();

if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {

$decoyip=$row['ipaddress'];
$ipaddressarray=explode('.',$decoyip);

$txt = "New-ADComputer -Name ".htmlspecialchars($key)." -DNSHostName ".htmlspecialchars($key).".$domainname";
$myfile = file_put_contents('/var/log/data/Decoycrumb.ps1', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
$txt = "dnscmd /recordadd $domainname ".htmlspecialchars($key)." A $decoyip";
$myfile = file_put_contents('/var/log/data/Decoycrumb.ps1', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
$txt = "dnscmd /recordadd $ipaddressarray[2].$ipaddressarray[1].$ipaddressarray[0].in-addr.arpa $ipaddressarray[3] PTR ".htmlspecialchars($key)."."."$domainname\n";
$myfile = file_put_contents('/var/log/data/Decoycrumb.ps1', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);


} //while loop end
} //if row not 0 end

} //if value end

} //for loop end

$name = '/var/log/data/Decoycrumb.ps1';
$fp = fopen($name, 'rb');

header('Content-Disposition: attachment; filename="DejavuDecoyCrumb.ps1"');
header("Content-Type: application/octet-stream");
header("Content-Length: " . filesize($name));

fpassthru($fp);
exit;

} else {

echo "<script>
alert('Please specify the domain name!');
window.location.href='crumbDecoy.php';
</script>";
exit();
} //if generatePS end
}

?>

  <!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>

<script type="text/javascript">

$(document).ready(function(){
// Listen for click on toggle checkbox
$('#select-all').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    } else {
        $(':checkbox').each(function() {
            this.checked = false;                       
        });
    }
});

});
</script>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Decoy to the Domain
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Decoy Management</a></li>
        <li class="active">Add Decoy to Domain</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     <form action="crumbDecoy.php" method="post">
      <div class="row">
        
       
            <!-- /.box-body -->
            <div class="col-md-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List Decoys</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered">
                <tbody>
                <tr>
                  <th>Decoy Name</th>
                  <th style="width: 200px">Decoys to Add
                    ( Select All <input type="checkbox" name="select-all" id="select-all" /> )
                  </th>
                </tr>
              <?php if(!empty($event)){
                  foreach ($event as $key => $value): 
              ?>
                <tr>
                  <td><?= dataFilter($event[$key]['decoyname'])?></td>
                  <td><input type="checkbox" name="<?= dataFilter($event[$key]['decoyname'])?>" value="Y"></td>
                </tr>
              <?php endforeach;
                }
              ?>
              </tbody></table>
              <br/>
	      <input type="text" class="form-control" name="domainname" placeholder="Domain Name (Example: mydomain.com)" style="width: 350px" required />
	      </br>
              <button type="submit" name="generatePS" value="Yes" class="btn btn-primary">Generate Powershell Script</button>
              <p></p>
              <p class="text-red">Note : Generated powershell script needs to run on the domain controller to create Computer Name & DNS entry for the above selected decoys.</p>
            </div>
    </form>
          </div>
          <!-- /.box -->
          <!-- /.box -->
        </div>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php include 'template/main-footer.php';?>
</body>
</html>
<?php
}
else 
{
  header('location:loginView.php');
}
?>
