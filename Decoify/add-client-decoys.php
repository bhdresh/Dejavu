<?php
if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

  if (isset($_POST['submit']))
  {
    if($_POST["decoyname"] == $_POST["decoygroup"]){
      echo "<script>
      alert('Decoy Name and Decoy Group cannot have same value!');
      window.location.href='add-server-decoys.php';
      </script>";
      exit();
    }

    if(val_input($_POST["interface"])){
        $interface=$_POST["interface"];
    }
    else{
      echo "<script>
      alert('Interface value Invalid');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
    }

    if(val_input($_POST["decoyname"])){
        $decoyname=$_POST["decoyname"];
    }
    else{
      echo "<script>
      alert('Decoy Name can only contain alpha numeric charcters');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
    }


    if(val_input($_POST["decoygroup"])){
        $decoygroup=$_POST["decoygroup"];
    }
    else{
      echo "<script>
      alert('Decoy Group can only contain alpha numeric charcters');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
    }

  if($_POST["NBNSCLIENT"] == 'on'){
    $NBNSCLIENT = 'on';
  }

  if($_POST["SSDPCLIENT"] == 'on'){
    $SSDPCLIENT = 'on';
  }


  if($_POST["ip_type"] == 'DHCP'){
        $ip_type = 'DHCP';
  }

  if($_POST["ip_type"] == 'Static'){
      
      if(val_ip($_POST["ipad"]) && val_ip($_POST["mask"]) && val_ip($_POST["GW"]))
      {
        $ipad=$_POST["ipad"];
        $mask=$_POST["mask"];
        $GW=$_POST["GW"];
      }
      else{
        echo "<script>
        alert('Please enter valid Static IP Details');
        window.location.href='add-client-decoys.php';
        </script>";
        exit();
      }
      $ip_type = 'Static';
  }


    if(!empty($_POST["macaddress"])){ 

    $macraw=$_POST["macaddress"];
    $macresult=preg_match('/^[0-9a-zA-Z]+:[0-9a-zA-Z]+:[0-9a-zA-Z]+:[0-9a-zA-Z]+:[0-9a-zA-Z]+:[0-9a-zA-Z]+/',$macraw,$outmac);
    if($macresult == '1'){
     $mac=$outmac[0]; 
    } else {
    
      echo "<script>
      alert('Invalid MAC Address');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
 
    }
    $mysqli = db_connect();
    $stmt = $mysqli->prepare("SELECT * FROM decoys WHERE macaddress=?");
    $stmt->bind_param("s", $mac);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = mysqli_fetch_assoc($result);

    if(!empty($row))
    {  
        echo "<script>
        alert('MAC Address ($mac) already exist. Please provide different MAC Address.');
        window.location.href='add-client-decoys.php';
        </script>";
        exit();
    }
    $stmt->close();

    }

    if(!empty($_POST["ttl"])){

    	$ttlraw=$_POST["ttl"];
    	$ttlresult=preg_match('/^[0-9]+/',$ttlraw,$outttl);
    	if($ttlresult == '1'){
     		$ttl=$outttl[0]; 
    		} else {
     
      		echo "<script>
      		alert('Invalid TTL value');
      		window.location.href='add-client-decoys.php';
      		</script>";
      		exit();

    		}
    }



  //check decoy name
    $mysqli = db_connect();

    $stmt = $mysqli->prepare("SELECT * FROM decoys WHERE decoyname=? or decoygroup=?");

    $stmt->bind_param("ss", $decoyname, $decoyname);

    $stmt->execute();

    $result = $stmt->get_result();

    $row = mysqli_fetch_assoc($result);     
          
    if(!empty($row)) 
    { 
      echo "<script>
      alert('Decoy name ($decoyname) already exist. Please provide different Decoy name.');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
    } 
    $stmt->close();

    //check decoy group
    $mysqli = db_connect();

    $stmt = $mysqli->prepare("SELECT * FROM decoys WHERE decoyname=?");

    $stmt->bind_param("s", $decoygroup);

    $stmt->execute();

    $result = $stmt->get_result();

    $row = mysqli_fetch_assoc($result);     
          
    if(!empty($row)) 
    { 
      echo "<script>
      alert('Network Location ($decoygroup) already exist. Please provide different network location.');
      window.location.href='add-client-decoys.php';
      </script>";
      exit();
    } 
    $stmt->close();


  if ($NBNSCLIENT == 'on' || $SSDPCLIENT == 'on')
  {
  	exec('sudo /sbin/ifconfig | egrep -i virtual[0-9]+ |awk -F "virtual" \'{print$2}\' |awk -F ":" \'{print$1}\'|sort -r| head -1|xargs|egrep -o "[0-9]+"',$output,$result);

  	$current_if_count=$output[0];

  	if ($current_if_count == false)
  	{
  		$current_if_count="999";
  	}

  	$new_if_count=$current_if_count + 1;

	if (!empty($mac))
        {
        exec("sudo /bin/ip link add link $interface virtual$new_if_count address $mac type macvlan",$outputadd,$result);
        } else {
        exec("sudo /bin/ip link add link $interface virtual$new_if_count type macvlan",$outputadd,$result);
        }
  	
	exec("sudo /sbin/ifconfig virtual$new_if_count up",$output,$result);

  	
  	if ($ip_type == 'DHCP')
  	{
  		exec("sudo /sbin/dhcpcd virtual$new_if_count",$output,$result);
  	} elseif ($ip_type == 'Static') {
  		exec("sudo /sbin/ifconfig virtual$new_if_count $ipad netmask $mask");
  		exec("sudo /sbin/route add default gw $GW dev virtual$new_if_count");
  	}


	$ipcheck=0;
        $ipa='';
            $mask='';
            $GW='';
            $cidrmask='';
            $networkadd='';
        while ($ipcheck == 0)
        {
                global $ipa, $mask, $GW, $mac;
                exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
                exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$4}'|grep [0-9]",$output4,$result);
                exec("sudo /sbin/route -n| grep -i virtual$new_if_count|awk -F \" \" '{print$2}'|grep -v \"0.0.0.0\"|grep [0-9]",$output5,$result);
		exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i ether|awk -F \" \" '{print$2}'|xargs",$output6,$result);

                $ipa=$output1[0];
                $mask=$output4[0];
                $GW=$output5[0];
		$mac=$output6[0];

                $ipcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$ipa,$out);

        }

        $networkaddcheck=0;
        while($networkaddcheck == 0)
        {
                global $cidrmask, $networkadd, $networkaddcheck;
                exec("sudo /usr/bin/sipcalc virtual$new_if_count|grep -i \"Network mask \"|grep -i \"bits\" |head -1|awk -F \"- \" '{print$2}'|xargs",$outputcidrmask,$result);
                exec("sudo /usr/bin/sipcalc virtual$new_if_count|grep -i \"Network address\"| head -1|awk -F \"- \" '{print$2}'",$outputnetworkadd,$result);

                $cidrmask=$outputcidrmask[0];
                $networkadd=$outputnetworkadd[0];
                $networkaddcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$networkadd,$out);
        }


// Modify TTL based on provided input

        if (!empty($ttl))
        {
        exec("sudo /sbin/iptables -t mangle -A POSTROUTING -s $ipa -j TTL --ttl-set $ttl",$outputttl,$resultttl);
        }



  	$mysqli = db_connect();

  	if ($stmt = $mysqli->prepare("INSERT INTO decoys (decoyname,decoygroup,services,interface,virinterface,ip_type,ipaddress,subnet,gateway,macaddress) VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?)"));
    {
    	$stmt->bind_param("sssssssss", $decoyname,$decoygroup,$interface,$newif,$ip_type,$ipa,$mask,$GW,$mac);
    	$newif="virtual".$new_if_count;
    	$stmt->execute(); 
    	$stmt->close();
    }


  	if ($NBNSCLIENT == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                        exec("sudo /usr/bin/docker run -d --name $decoyname\"_nbnsclient\" nbnsclient",$outputnbnsclient2,$resultnbnsclient2);
  			exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$output,$result);
                  }
  		  exec("sudo /bin/sh /var/log/data/pipework.sh --direct-phys virtual$new_if_count $decoyname\"_nbnsclient\" $ipa/$mask@$GW",$nbnsclientoutput,$result);
                  sleep (5);
                  exec("sudo /usr/bin/docker inspect $decoyname\"_nbnsclient\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/tmp\/nbns.log/g\"",$outputnbnsclient9,$resultnbnsclient);


                  //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'NBNSCLIENT; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'nbnsclient',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputnbnsclient9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  //
  		            sleep (5);

                  exec("sudo /usr/bin/nohup /bin/sh log.sh \"nbnsclient\" \"$outputnbnsclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" > /dev/null 2>&1 &",$nbnsclientlog,$result);
          }

	


	if ($SSDPCLIENT == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                        exec("sudo /usr/bin/docker run -d --name $decoyname\"_ssdpclient\" ssdpclient",$outputssdpclient2,$resultssdpclient2);
                        exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$output,$result);
                  }
                  exec("sudo /bin/sh /var/log/data/pipework.sh --direct-phys virtual$new_if_count $decoyname\"_ssdpclient\" $ipa/$mask@$GW",$ssdpclientoutput,$result);
                  sleep (5);
                  exec("sudo /usr/bin/docker inspect $decoyname\"_ssdpclient\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/tmp\/ssdpclient.log/g\"",$outputssdpclient9,$resultssdpclient);


                  //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'SSDPCLIENT; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'ssdpclient',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputnbnsclient9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  //
                            sleep (5);

                  exec("sudo /usr/bin/nohup /bin/sh log.sh \"ssdpclient\" \"$outputssdpclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" > /dev/null 2>&1 &",$ssdpclientlog,$result);
          }

		exec("sudo /sbin/iptables -t nat -I PREROUTING -d $ipa -j LOG",$outputmainlogpid,$resultmainlogpid);

		

  		mysqli_close($conn);
  		echo "<script>
		alert('Decoy added successfully');
  		window.location.href='add-client-decoys.php';
  		</script>";
  		exit();


  }

  }

?>
<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        New Client Decoy
        <small>Add</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Decoy Management</a></li>
        <li class="active">Add Client Decoys</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">New Decoy</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form role="form" action="add-client-decoys.php" method="post">
                <div class="form-group" style="width: 150px">
                  <label>Physical Interface</label>
                  <select class="form-control" name="interface">
                    <?php

		      exec('sudo /sbin/ifconfig | grep "flags="| egrep -v "veth|lo|virtual|docker|eth0"|awk -F ":" \'{print$1}\'',$output,$result);
                      for($i=0;$i<sizeof($output);$i++) {
                        echo '<option>'.$output[$i].'</option>';
      
                      }
                      
                    ?>
                  </select>
                </div>
                <!-- text input -->


                <div class="form-group">
                  <label>Decoy Name</label>
                  <input type="text" class="form-control" name="decoyname" placeholder="DecoyName" style="width: 550px" required />
                </div>
                <div class="form-group">
                  <label>Network Location</label>
                  <input type="text" class="form-control" name="decoygroup" placeholder="IT" style="width: 550px" required />
                </div>



                <script type="text/javascript">
                    function EnableTextBox(Static) {
                        var box1 = document.getElementById("box1");
                        box1.disabled = false;
                        var box1 = document.getElementById("box2");
                        box2.disabled = false;
                        var box1 = document.getElementById("box3");
                        box3.disabled = false;
                    }
                    function DisableTextBox(DHCP) {
                        var box1 = document.getElementById("box1");
                        box1.disabled = true;
                        var box1 = document.getElementById("box2");
                        box2.disabled = true;
                        var box1 = document.getElementById("box3");
                        box3.disabled = true;
                    }
                </script>


                <div class="form-group">
                  <label>
                      <input type="radio" name="ip_type" value="DHCP" id="DHCP" onclick="DisableTextBox(this)" checked>
                      DHCP
                      &nbsp;&nbsp;
                  </label>
                  <label>
                      <input type="radio" name="ip_type" value="Static" id="Static" onclick="EnableTextBox(this)" >
                      Static
                  </label>
                
              </div>

              <!-- IP mask -->
              <div class="form-group">
                <label>IP Address:</label>

                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-laptop"></i>
                  </div>
                  <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask style="width: 510px" id="box1" name="ipad" disabled="disabled">
                </div>
                <!-- /.input group -->
              </div>

                <!-- Subnet mask -->
              <div class="form-group">
                <label>Subnet:</label>

                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-laptop"></i>
                  </div>
                  <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask style="width: 510px" id="box2" name="mask" disabled="disabled">
                </div>
                <!-- /.input group -->
              </div>

                <!-- Gateway mask -->
              <div class="form-group">
                <label>Gateway:</label>

                <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-laptop"></i>
                  </div>
                  <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask style="width: 510px" id="box3" name="GW" disabled="disabled">
                </div>
                <!-- /.input group -->
              </div></br>

                <!-- checkbox -->

                <div class="form-group">
		  <label>
                      <input type="checkbox" class="flat-red" name="NBNSCLIENT">
                      NBNS Client
                  </label>&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;

                  <label>
                      <input type="checkbox" class="flat-red" name="SSDPCLIENT">
                      SSDP Client
                  </label></br>

                </div>


		<script type="text/javascript">

                  $(document).ready(function(){
                    $("#CustomizeDecoy").click(function(){
                      $("#custom_val").toggle();
                      $("#minus_cust").toggle();
                      $("#add_cust").toggle();
                      
                    });
                    
                  });
                  </script>
	<!--
                <label id="CustomizeDecoy">
                    <i class="fa fa-fw fa-plus" id="add_cust" style="color:#808080"></i> 
                    <i class="fa fa-fw fa-minus" id="minus_cust" style="display:none"></i>
                    <font color="#808080">Advanced Config</font>
                </label>
                
                <div id="custom_val" style="display:none">
                  <div class="form-group">
                  <label>Custom MAC Address</label>
                  <input type="text" class="form-control" name="macaddress" placeholder="00:00:00:00:00:00" style="width: 550px"/>
                </div>
                <div class="form-group">
                  <label>Set TTL Value</label>
                  <input type="text" class="form-control" name="ttl" placeholder="128" style="width: 550px"/>
                </div>
                </div>
	-->
                <div class="box-footer">
                  <button type="submit" name="submit" value="Yes" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.box-body -->
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
