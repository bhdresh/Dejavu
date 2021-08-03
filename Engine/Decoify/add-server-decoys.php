<?php

if(!isset($_SESSION)) 
{ 
    session_start(); 
}

include "db.php";

if(isset($_SESSION['user_name']) && $_SESSION['role'] == 'admin') {

    function showFiles()
    {
        $mysqli = db_connect();

        $stmt = $mysqli->prepare("SELECT id, file_name FROM FileDetails where Status=1");
        
        $stmt->execute();

        $result = $stmt->get_result();
        
        if($result->num_rows === 0) {
          
          $stmt->close();

          return $event;
        }

        else{
          while($row = $result->fetch_array()) {

            $event[] = $row;
          
          }

          $stmt->close();

          return $event;
        }
    }

    $event = showFiles();
    
    if (isset($_POST['submit']) && $_SESSION['csrf_token'] == $_POST['csrf_token'])
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
      window.location.href='add-server-decoys.php';
      </script>";
      exit();
    }

    if(val_input($_POST["decoyname"])){
        $decoyname=$_POST["decoyname"];
    }
    else{
      echo "<script>
      alert('Decoy Name can only contain alpha numeric charcters');
      window.location.href='add-server-decoys.php';
      </script>";
      exit();
    }


    if(val_input($_POST["decoygroup"])){
        $decoygroup=$_POST["decoygroup"];
    }
    else{
      echo "<script>
      alert('Decoy Group can only contain alpha numeric charcters');
      window.location.href='add-server-decoys.php';
      </script>";
      exit();
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
          window.location.href='add-server-decoys.php';
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
      window.location.href='add-server-decoys.php';
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
        window.location.href='add-server-decoys.php';
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
      		window.location.href='add-server-decoys.php';
      		</script>";
      		exit();

    		}
    }



    if($_POST["SMB"] == 'on'){
      $SMB= 'on'; 
    }

    if($_POST["FTP"] == 'on'){
      $FTP= 'on'; 
    }

    if($_POST["SSH"] == 'on'){
      $SSH= 'on'; 
    }
    if($_POST["TELNET"] == 'on'){
      $TELNET= 'on';
    }
    if($_POST["RDP"] == 'on'){
      $RDP= 'on';
    }
    if($_POST["MSSQL"] == 'on'){
      $MSSQL= 'on';
    }
    if($_POST["VNC"] == 'on'){
      $VNC= 'on';
    }
    if($_POST["TFTP"] == 'on'){
      $TFTP= 'on';
    }
    if($_POST["MYSQL"] == 'on'){
      $MYSQL= 'on'; 
    }

    if($_POST["MODBUS"] == 'on'){
      $MODBUS= 'on';
    }

    if($_POST["SNMP"] == 'on'){
      $SNMP= 'on';
    }

    if($_POST["S7COMM"] == 'on'){
      $S7COMM= 'on';
    }

    if($_POST["WEB"] == 'on'){
      $WEB= 'on'; 
    }

    if($_POST["HONEYCOMB"] == 'on'){
      $HONEYCOMB= 'on';
    }


    if($_POST["webservertype"] == 'tomcat'){
      $webservertype = 'tomcat';
    }

    if($_POST["webservertype"] == 'apache'){
      $webservertype = 'apache';
    }

    if($_POST["custom-ssh"] == 'interactivessh'){
      $sshtype = 'SSH - Interactive';
      $sshdecoyimage = 'ssh1d';
      $sshport = '2222';
    } else {
      $sshtype = 'SSH - Noninteractive';
      $sshdecoyimage = 'sshd';
      $sshport = '22';
    }

    if($_POST["custom-rdp"] == 'interactiverdp'){
      $rdptype = 'RDP - Interactive';
      $rdpdecoyimage = 'pyrdp';
      if(val_input($_POST["realRDPIP"])){
      	$realRDPIP = $_POST['realRDPIP'];
      }
    } else {
      $rdptype = 'RDP - Noninteractive';
      $rdpdecoyimage = 'honeyserver';
    }


    if($_POST["webservertype"] == 'basicauth'){
      $webservertype = 'basicauth';
    }

    if($_POST["custom-page"] == 'default-owa'){
      $apachedecoyfile ='DEFAULTOWA.zip';
    } elseif($_POST["custom-page"] == 'default-f5login'){
      $apachedecoyfile ='DEFAULTVPN.zip'; 
    } else {
      $apachedecoyfile = escapeshellarg($_POST["custom-page"]);
    }

    if($_POST["custom-smbpage"] == true){
      $smbdecoyfile = escapeshellarg($_POST["custom-smbpage"]);
    }

    if($_POST["decoy_type"] == 'external')
    {
      $decoy_type = 2;
    }
    else {
      $decoy_type = 1;
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
    	window.location.href='add-server-decoys.php';
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
      window.location.href='add-server-decoys.php';
      </script>";
      exit();
    } 
    $stmt->close();


//main docker if loop

    if ($SSH == 'on' || $RDP == 'on' || $TFTP == 'on' || $VNC == 'on' || $MSSQL == 'on' || $TELNET == 'on' || $SMB == 'on' || $FTP == 'on' || $MYSQL == 'on' || $WEB == 'on' || $MODBUS == 'on' || $SNMP == 'on' || $S7COMM == 'on' || $HONEYCOMB == 'on')
    {
	exec('sudo /bin/cp -f /etc/resolv.conf /etc/resolv.conf.bkp',$outputdns,$resultdns);
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
        exec('sudo /usr/bin/shuf -n 1 /etc/macvendorlist |egrep -o "[0-9a-zA-Z]+:[0-9a-zA-Z]+:[0-9a-zA-Z]+:?"|tr "[A-Z]" "[a-z]"|head -1|sed "s/$/`openssl rand -hex 3 | sed "s/\(..\)/\1:/g; s/:$//"`/g"',$outputrandommac,$resultrandommac);
	$mac=$outputrandommac[0];	
	exec("sudo /bin/ip link add link $interface virtual$new_if_count address $mac type macvlan",$outputadd,$result);
        //exec("sudo /bin/ip link add link $interface virtual$new_if_count type macvlan",$outputadd,$result);

        }

    	exec("sudo /sbin/ifconfig virtual$new_if_count up",$outputconfig,$result);
	exec("sudo /bin/ip link set promisc on dev virtual$new_if_count",$outputpromisc,$resultpromisc);
    	
    	if ($ip_type == 'DHCP')
    	{
    		exec("sudo /sbin/dhcpcd virtual$new_if_count",$outputdhcp,$result);
    	} elseif ($ip_type == 'Static') {
    		exec("sudo /sbin/ifconfig virtual$new_if_count $ipad netmask $mask");
    		exec("sudo /sbin/route add default gw $GW dev virtual$new_if_count");
    	}

    	$ipcheck=0;
	$ipcheckloop=0;
    	$ipa='';
            $mask='';
            $GW='';
            $cidrmask='';
            $networkadd='';
    	while ($ipcheckloop < 10 && $ipcheck == 0)
    	{
		global $ipa, $mask, $GW, $mac, $ipcheck;
		exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
                exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$4}'|grep [0-9]",$output4,$result);
                exec("sudo /sbin/route -n| grep -i virtual$new_if_count|awk -F \" \" '{print$2}'|grep -v \"0.0.0.0\"|grep [0-9]",$output5,$result);
		exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i ether|awk -F \" \" '{print$2}'|xargs",$output6,$result);

    		$ipa=$output1[0];
    		$mask=$output4[0];
    		$GW=$output5[0];
		$mac=$output6[0];

    		$ipcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$ipa,$out);	
		sleep(3);

		$ipcheckloop++;

    	}

	if ($ipcheck == 0)
	{
		exec("sudo /bin/ip link del virtual$new_if_count",$output7,$result7);
		echo "<script>
      		alert('No DHCP lease. Try again or configure static IP address.');
      		window.location.href='add-server-decoys.php';
      		</script>";
      		exit();
	
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


// Adding policy based routing table	
	 exec("sudo /bin/ip route flush $new_if_count",$outputflush,$result);
         exec("sudo /bin/ip route add table $new_if_count to $networkadd/$cidrmask dev virtual$new_if_count",$outputadd1,$result);
         exec("sudo /bin/ip route add table $new_if_count to default via $GW dev virtual$new_if_count",$outputadd2,$result);
         exec("sudo /bin/ip rule add from $ipa/32 table $new_if_count priority 11",$outputadd4,$result);
         exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$outputdel1,$result);
// Restoring DNS settings
	exec('sudo /bin/cp -f /etc/resolv.conf.bkp /etc/resolv.conf',$outputdns1,$resultdns1);

    	$mysqli = db_connect();

    	if ($stmt = $mysqli->prepare("INSERT INTO decoys (decoyname,decoygroup,services,interface,virinterface,ip_type,ipaddress,subnet,gateway,macaddress,ttl,decoy_type) VALUES (?, ?, '',?, ?, ?, ?, ?, ?, ?, ?, ?)"));{
      	$stmt->bind_param("ssssssssssi", $decoyname,$decoygroup,$interface,$newif,$ip_type,$ipa,$mask,$GW,$mac,$ttl,$decoy_type);
      	$newif="virtual".$new_if_count;
      	$stmt->execute(); 
      	$stmt->close();
      		}

    	if ($SMB == 'on')
    	{
    		$dockerip=trim(' ');
    		$password=bin2hex(openssl_random_pseudo_bytes(50));	
    		if (!empty($ipa))
    		{
    			#exec("sudo /usr/bin/docker run -d --name $decoyname\"_smbd\" -p $ipa:139:139 -p $ipa:445:445 --memory=\"256m\" -d smbd -g \"log level = 3\" -g \"map to guest = Never\" -S -s \"files;/tmp/;yes;yes;yes;all\" -u \"administrator;$password\"",$output2,$result);
			#exec("sudo /usr/bin/docker run -d --hostname $decoyname --name $decoyname\"_smbd\" -p $ipa:139:139 -p $ipa:445:445 --memory=\"256m\" -d smbd -g \"log level = 3\" -g \"map to guest = Bad User\" -S -s \"files;/tmp/;yes;yes;yes;all\"",$output2,$result);
			exec("sudo /usr/bin/docker run -d --hostname $decoyname --name $decoyname\"_smbd\" -p $ipa:139:139 -p $ipa:445:445 --memory=\"256m\" -d smbd",$output2,$result);

			$dockeripcheck=0;
    			while ($dockeripcheck == 0)
    			{
				global $dockeripcheck;
    				exec("sudo /usr/bin/docker inspect $decoyname\"_smbd\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputsmb7,$resultsmb);
    				$dockerip=$outputsmb7[0];
				$dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputsmb7[0],$out);

    			}
			//Updating policy based routing table
    			exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputadd3,$result);
			//
    		}	
    		#exec("sudo /bin/sh ./pipework.sh --direct-phys virtual$new_if_count $decoyname\"_smbd\" $ipa/$mask@$GW",$output6,$result);
    		exec("sudo /usr/bin/docker inspect $decoyname\"_smbd\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$output3,$result);


		if ($smbdecoyfile == true)
                {
                        exec("sudo /usr/bin/docker inspect $decoyname\"_smbd\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputsmb10,$resultsmb);
                        exec("sudo /bin/sh -c \"cd $outputsmb10[0]/tmp/files; unzip -o /var/dejavufiles/uploads/$smbdecoyfile\"",$outputsmb11,$resultsmb);
                }


            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'SMB; '), smbdecoyfile=? where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("ss", $smbdecoyfile,$decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'smbd',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $output3[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
    		
    		exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"smbd\" \"$output3[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

    	}

    	if ($FTP == 'on')
    	{
    		$dockerip=trim(' ');
    		$password=bin2hex(openssl_random_pseudo_bytes(50));
    		if (!empty($ipa))
    		{
    			exec("sudo /usr/bin/docker run -d --name $decoyname\"_ftpd\" -p $ipa:21:21 --memory=\"256m\" -e FTP_USER=$password -e FTP_PASS=$password -e FTP_BANNER='vsFTPd 2.2.2' ftpd",$output2,$result);
			$dockeripcheck=0;
                        while ($dockeripcheck == 0)
    			{
				global $dockeripcheck;
    				exec("sudo /usr/bin/docker inspect $decoyname\"_ftpd\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputftp8,$resultftp);
    				$dockerip=$outputftp8[0];
				$dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputftp8[0],$out);
    			}
    			exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$output,$result);
    		}
	
		exec("sudo /usr/bin/docker inspect $decoyname\"_ftpd\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$output9,$result);

    		//
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'FTP; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'ftpd',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $output9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
		exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"ftpd\" \"$output9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

    		$test=output9[0];
    	}



   	if ($TELNET == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_telnet\" -p $ipa:23:23 --memory=\"256m\" honeyserver",$outputtelnet2,$resulttelnet2);
			    $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_telnet\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputtelnet8,$resulttelnet);
                                    $dockerip=$outputtelnet8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputtelnet8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputtelnet,$resulttelnet);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_telnet\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputtelnet9,$resulttelnet);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'TELNET; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'telnet',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputtelnet9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            // 
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"telnet\" \"$outputtelnet9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputtelnet9[0];
            }


       if ($MSSQL == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_mssql\" -p $ipa:1433:1433 --memory=\"256m\" honeyserver",$outputmssql2,$resultmssql2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_mssql\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputmssql8,$resultmssql);
                                    $dockerip=$outputmssql8[0];
                                    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputmssql8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputmssql,$resulmssql);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_mssql\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputmssql9,$resultmssql);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'MSSQL; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'mssql',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputmssql9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"mssql\" \"$outputmssql9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputmssql9[0];
            }



        if ($RDP == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
			    if (!empty($realRDPIP))
			    {
			 	exec("sudo /sbin/iptables -t mangle -I PREROUTING -i docker0 -d $realRDPIP -j ACCEPT",$outputrdpout,$resultrdpout);
			    }
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_$rdpdecoyimage\" -p $ipa:3389:3389 --memory=\"500m\" $rdpdecoyimage $realRDPIP",$outputrdp2,$resultrdp2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_$rdpdecoyimage\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputrdp8,$resultrdp);
                                    $dockerip=$outputrdp8[0];
                                    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputrdp8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputrdp,$resultrdp);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_$rdpdecoyimage\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputrdp9,$resultrdp);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'$rdptype; '),decoycomip=? where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("ss", $realRDPIP, $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,?,?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("sssss", $decoyname, $rdpdecoyimage, $dockerip, $new_if_count, $outputrdp9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"$rdpdecoyimage\" \"$outputrdp9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputrdp9[0];
            }



       if ($VNC == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_vnc\" -p $ipa:5000:5000 --memory=\"256m\" honeyserver",$outputvnc2,$resultvnc2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_vnc\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputvnc8,$resultvnc);
                                    $dockerip=$outputvnc8[0];
                                    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputvnc8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputvnc,$resultvnc);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_vnc\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputvnc9,$resultvnc);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'VNC; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'vnc',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputtelnet9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"vnc\" \"$outputvnc9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputvnc9[0];
            }




      	if ($TFTP == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_tftp\" -p $ipa:69:69/UDP --memory=\"256m\" honeyserver",$outputtftp2,$resulttftp2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_tftp\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputtftp8,$resulttftp);
                                    $dockerip=$outputtftp8[0];
                                    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputtftp8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputtftp,$resulttftp);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_tftp\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputtftp9,$resulttftp);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'TFTP; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'tftp',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputtelnet9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"tftp\" \"$outputtftp9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputtftp9[0];
            }






    	if ($SSH == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --hostname $decoyname --name $decoyname\"_$sshdecoyimage\" -p $ipa:22:$sshport --memory=\"256m\" $sshdecoyimage",$outputssh2,$resultssh2);
			    $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_$sshdecoyimage\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputssh8,$resultssh);
                                    $dockerip=$outputssh8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputssh8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputssh,$resultssh);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_$sshdecoyimage\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'",$outputssh9,$resultssh);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'$sshtype; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,?,?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("sssss", $decoyname, $sshdecoyimage, $dockerip, $new_if_count, $outputssh9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            // 
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"$sshdecoyimage\" \"$outputssh9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputssh9[0];
            }





    	if ($HONEYCOMB == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_honeycomb\" -p $ipa:80:80 -p $ipa:443:443 --memory=\"256m\" honeycomb",$outputhoneycomb2,$resulthoneycomb2);
			    $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_honeycomb\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputhoneycomb8,$resulthoneycomb);
                                    $dockerip=$outputhoneycomb8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputhoneycomb8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputhoneycomb,$resulthoneycomb);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_honeycomb\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputhoneycomb9,$resulthoneycomb);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'HONEYCOMB; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'honeycomb',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputssh9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            // 
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"honeycomb\" \"$outputhoneycomb9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

                    $test=outputhoneycomb9[0];
            }







    	if ($MYSQL == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_mysql\" -p $ipa:3306:3306 --memory=\"256m\" honeyserver",$outputmysql2,$resultmysql2);
    			    $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_mysql\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputmysql8,$resultmysql);
                                    $dockerip=$outputmysql8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputmysql8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputmysql,$resultmysql);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_mysql\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/mysql_general.log/g\"",$outputmysql9,$resultmysql);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'MYSQL; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'mysql',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputmysql9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            // 
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"mysql\" \"$outputmysql9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

            }


	

    	if ($WEB == 'on')
            {
    		if ($webservertype == 'tomcat')
    		{
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
    		
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_tomcat\" -p $ipa:8080:8080 --memory=\"256m\" tomcat",$outputtomcat2,$resulttomcat2);
			                       $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_tomcat\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputtomcat8,$resulttomcat);
                                    $dockerip=$outputtomcat8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputtomcat8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputtomcat,$resulttomcat);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_tomcat\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputtomcat9,$resulttomcat);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'TOMCAT; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'tomcat',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputtomcat9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  // 
                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"tomcat\" \"$outputtomcat9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);
    		}


    		if ($webservertype == 'apache')
                    {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {

                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_apache\" -p $ipa:80:80 -p $ipa:443:443 --memory=\"256m\" httpd /usr/sbin/apache2ctl -D FOREGROUND",$outputapache2,$resultapache2);
			    $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
				    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_apache\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputapache8,$resultapache);
                                    $dockerip=$outputapache8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputapache8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputapache,$resultapache);
                    }
    		          exec("sudo /usr/bin/docker inspect $decoyname\"_apache\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputapache9,$resultapache);

                  if ($apachedecoyfile == true)
                  {
                    exec("sudo /usr/bin/docker inspect $decoyname\"_apache\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputapache10,$resultapache);
		    exec("sudo /usr/bin/docker exec $decoyname\"_apache\" rm -f /var/www/html/index.html",$outputapache12,$resultapache);
                    exec("sudo /bin/sh -c \"cd $outputapache10[0]/var/www/html/; unzip -o /var/dejavufiles/uploads/$apachedecoyfile\"",$outputapache11,$resultapache);

		
                  }

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'APACHE; '), apachedecoyfile=? where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("ss", $apachedecoyfile,$decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'apache',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputapache9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            // 
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"apache\" \"$outputapache9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);
                    }


		if ($webservertype == 'basicauth')
                {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {

                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_basicauth\" -p $ipa:8080:8080 --memory=\"256m\" basicauth python2.7 /etc/bap.py",$outputbasicauth2,$resultbasicauth2);
                                               $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_basicauth\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputbasicauth8,$resultbasicauth);
                                    $dockerip=$outputbasicauth8[0];
                                    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputbasicauth8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputbasicauth,$resultbasicauth);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_basicauth\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/var\/log\/messages/g\"",$outputbasicauth9,$resultbasicauth);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'WEB-BASIC-AUTH; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'basicauth',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputbasicauth9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  //
                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"basicauth\" \"$outputbasicauth9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);
                }

            }



        if ($MODBUS == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_modbus\" -p $ipa:502:502 --memory=\"256m\" ics conpot -f --template default",$outputmodbus2,$resultmosdbus2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_modbus\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputmodbus8,$resultmodbus);
                                    $dockerip=$outputmodbus8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputmodbus8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputmodbus,$resultmodbus);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_modbus\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputmodbus9,$resultmodbus);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'MODBUS; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'modbus',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputmodbus9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"modbus\" \"$outputmodbus9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

            }






        if ($SNMP == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_snmp\" -p $ipa:161:161/udp --memory=\"256m\" ics conpot -f --template default",$outputsnmp2,$resultsnmp2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_snmp\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputsnmp8,$resultsnmp);
                                    $dockerip=$outputsnmp8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputsnmp8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputsnmp,$resultsnmp);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_snmp\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputsnmp9,$resultsnmp);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'SNMP; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'snmp',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputsnmp9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"snmp\" \"$outputsnmp9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

            }



      if ($S7COMM == 'on')
            {
                    $dockerip=trim(' ');

                    if (!empty($ipa))
                    {
                            exec("sudo /usr/bin/docker run -d --name $decoyname\"_s7comm\" -p $ipa:102:102 --memory=\"256m\" ics conpot -f --template default",$outputs7comm2,$results7comm2);
                            $dockeripcheck=0;
                            while ($dockeripcheck == 0)
                            {
                                    global $dockeripcheck;
                                    exec("sudo /usr/bin/docker inspect $decoyname\"_s7comm\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputs7comm8,$results7comm);
                                    $dockerip=$outputs7comm8[0];
				    $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputs7comm8[0],$out);
                            }
                            exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputs7comm,$results7comm);
                    }
                    exec("sudo /usr/bin/docker inspect $decoyname\"_s7comm\"| grep -i \"LogPath\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$//g\"",$outputs7comm9,$results7comm);

            //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'S7COMM; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'s7comm',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputs7comm9[0]);

                  $stmt2->execute();

                  $stmt2->close();

            //
                    exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"s7comm\" \"$outputs7comm9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$output,$result);

            }






		exec("sudo /sbin/iptables -t nat -I PREROUTING -d $ipa -j LOG",$outputmainlogpid,$resultmainlogpid);


    		mysqli_close($conn);
    		echo "<script>
		alert('Decoy added successfully');
    		window.location.href='add-server-decoys.php';
    		</script>";
    		exit();


    }

//docker main if completed

    }
?>

<!-- Header.php. Contains header content -->
<?php include 'template/header.php';?>
<body class="hold-transition skin-black-light sidebar-mini">
<div class="wrapper">

<?php include 'template/main-header.php';?>
 <!-- Left side column. contains the logo and sidebar -->
<?php include 'template/main-sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        New Server Decoy
        <div id="loader-img" style="display:none;">
          <small>Checking API Connection for Logs</small>
          <img src="template/loader.gif" style="height:40px; width:40 px"/ >
        </div>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Decoy Management</a></li>
        <li class="active">Add Server Decoys</li>
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
              <form role="form" action="add-server-decoys.php" method="post">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"/>
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
                
                <div class="form-group" style="width: 150px">
                  <label>Decoy Type</label>
                  <select class="form-control" name="decoy_type">
                      <option value="internal" selected="selected">Internal Decoy</option>
                      <option value="external">External Decoy</option>
                  </select>
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
                      <input type="checkbox" class="flat-red" name="MYSQL">
                      MYSQL
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;
                  </label>

                  <label>
                      <input type="checkbox" class="flat-red" name="SNMP">
                      SNMP
		      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;
                  </label>
		  <label>
                     <input type="checkbox" class="flat-red" name="TELNET">
                     TELNET
                  </label>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

		<label>
                      <input type="checkbox" class="flat-red" name="SMB" id="smbservercheck">
                     SMB
			&nbsp;&nbsp;&nbsp;&nbsp;
                      <select name="custom-smbpage" id="custom-smbpage" style="display:none">
                        <option value="" selected="selected">Select Custom Files</option>
                        <?php if(!empty($event)){
                            foreach ($event as $key => $value):
                        ?>
                        <option value="<?= dataFilter($event[$key]['file_name'])?>">Custom - <?= dataFilter($event[$key]['file_name'])?></option>
                        <?php endforeach;
                          }
                        ?>
                      </select> 
                  </label>




</br>

                  <label>
                      <input type="checkbox" class="flat-red" name="FTP">
                      FTP
		      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;
                  </label>

		  <label>
                      <input type="checkbox" class="flat-red" name="S7COMM">
                      ICS-S7COMM
		      &nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;
                  </label>

		  <label>
                     <input type="checkbox" class="flat-red" name="TFTP">
                     TFTP
                  </label>
                  &nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;

		  <label>
                      <input type="checkbox" class="flat-red" name="WEB" id="webservercheck">
                      WEB SERVER
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <select name="webservertype" id="webservertype" style="display:none">
                        <option value="tomcat">Tomcat</option>
                        <option value="apache">Apache</option>
			<option value="basicauth">Basic Web Auth</option>
                      </select>
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <select name="custom-page" id="custom-page" style="display:none">
                        <option value="" selected="selected">Select Custom Web Page</option>
                        <option value="default-owa">Default - OWA Login</option>
                        <option value="default-f5login">Default - F5 VPN Login</option>
                        <?php if(!empty($event)){
                            foreach ($event as $key => $value): 
                        ?>
                        <option value="<?= dataFilter($event[$key]['file_name'])?>">Custom - <?= dataFilter($event[$key]['file_name'])?></option>
                        <?php endforeach;
                          }
                        ?>
                      </select>

                  </label>
                  </br>

                  <script type="text/javascript">

                  $(document).ready(function(){

                     $('#webservercheck').change(function () {
                      $("#webservertype").toggle();
                      $("#custom-page").hide();
                      $("#webservertype").val('tomcat');

                    });

                    $("#webservertype").change(function(){
                    
                      if ( $(this).val() == "apache" ) { 
                        $("#custom-page").show();
                      }
                      if( $(this).val() == "tomcat" ) { 
                         $("#custom-page").hide();
                      }
                       
                    });
			$('#smbservercheck').change(function () {
                          $("#custom-smbpage").toggle();
                    }); 
	
			$('#sshservercheck').change(function () {
                          $("#custom-ssh").toggle();
                    });
		     
		    
                    $("#CustomizeDecoy").click(function(){
                      $("#custom_val").toggle();
		       $("#minus_cust").toggle();
                      $("#add_cust").toggle();
                    });

		    $('#rdpservercheck').change(function () {
                          $("#custom-rdp").toggle();
                    });

                    $('#custom-rdp').change(function () {
                      if ( $(this).val() == "interactiverdp" ) { 
                        $("#realRDPIP").toggle();
                      }
                      else{
                        $("#realRDPIP").hide();
                      }
                    });
                    
                  });
                  </script>
                  <label>
                      <input type="checkbox" class="flat-red" name="MSSQL">
                      MSSQL  
		      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;
                  </label>

	          <label>
                      <input type="checkbox" class="flat-red" name="MODBUS">
                      ICS-MODBUS
		      &nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;	
       	          </label>
		  <label>
                     <input type="checkbox" class="flat-red" name="HONEYCOMB">
                     HONEYCOMB 
                  </label>
		  &nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;
		  <label>
                      <input type="checkbox" class="flat-red" name="SSH" id="sshservercheck">
                      SSH
                      &nbsp;&nbsp;&nbsp;&nbsp;
                      <select name="custom-ssh" id="custom-ssh" style="display:none">
                        <option value="noninteractivessh">Noninteractive</option>
                        <option value="interactivessh">Interactive</option>
                      </select>
                  </label>
                  </br>
		  <label>
                     <input type="checkbox" class="flat-red" name="VNC">
		     VNC
                  </label>
		  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;

		  <label>
                     <input type="checkbox" class="flat-red" name="RDP" id="rdpservercheck">
                     RDP 
                     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <select name="custom-rdp" id="custom-rdp" style="display:none">
                      <option value="noninteractiverdp" id="noninteractiverdp">Noninteractive</option>
                      <option value="interactiverdp">Interactive</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <input type="text" name="realRDPIP" placeholder="Real RDP Server IP" id="realRDPIP" style="display:none" />
                  </label>

              <p></p>
		
		<label id="CustomizeDecoy">
                    <i class="fa fa-fw fa-plus" id="add_cust" style="color:#808080"></i> 
                    <i class="fa fa-fw fa-minus" id="minus_cust" style="display:none"></i>
                    <font color="#808080">Advanced Config</font>
	              </label>
                </div>
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
