<?php


$val = getopt(null, ["interface:", "decoyname:", "decoygroup:", "NBNSCLIENT:", "SSDPCLIENT:", "ARPMITM:", "EMAILCLIENT:", "ip_type:", "ipad:", "mask:", "GW:", "macaddress:", "ttl:", "decoy_type:", "imap_email:", "imap_pass:", "imap_server:", "imap_port:"]);

include "db.php";

    $interface=$val["interface"];
    $decoyname=$val["decoyname"];
    $decoygroup=$val["decoygroup"];
    $NBNSCLIENT=$val["NBNSCLIENT"];
    $SSDPCLIENT=$val["SSDPCLIENT"];
    $ARPMITM=$val["ARPMITM"];
    $EMAILCLIENT=$val["EMAILCLIENT"];
    $ip_type=$val["ip_type"];
    $ipad=$val["ipad"];
    $mask=$val["mask"];
    $GW=$val["GW"];
    $mac=$val["macaddress"];
    $ttl=$val["ttl"];
    $decoy_type=$val["decoy_type"];
    $imap_email=escapeshellarg($val["imap_email"]);
    $imap_pass=escapeshellarg($val["imap_pass"]);
    $imap_server=escapeshellarg($val["imap_server"]);
    $imap_port=escapeshellarg($val["imap_port"]);

  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT * FROM decoys WHERE decoyname=?");

  $stmt->bind_param("s", $decoyname);
  
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


  if ($NBNSCLIENT == 'on' || $SSDPCLIENT == 'on' || $ARPMITM == 'on' || $EMAILCLIENT == 'on' )
  {
	exec('sudo /bin/cp -f /etc/resolv.conf /etc/resolv.conf.bkp',$outputdns,$resultdns);
  	exec('sudo /sbin/ifconfig | egrep -i virtual[0-9]+ |awk -F "virtual" \'{print$2}\' |awk -F ":" \'{print$1}\'|sort -r| head -1|xargs|egrep -o "[0-9]+"',$output,$result);

  	$current_if_count=$output[0];

  	if ($current_if_count == false)
  	{
  		$current_if_count="999";
  	}

  	$new_if_count=$current_if_count + 1;


	if ($mac == true)
        {
        exec("sudo /bin/ip link add link $interface virtual$new_if_count address $mac type macvlan",$outputadd,$result);
        } else {
        exec("sudo /bin/ip link add link $interface virtual$new_if_count type macvlan",$outputadd,$result);
        }

  	exec("sudo /sbin/ifconfig virtual$new_if_count up",$output,$result);
	exec("sudo /bin/ip link set promisc on dev virtual$new_if_count",$outputpromisc,$resultpromisc);
  	
  	if ($ip_type == 'DHCP')
  	{
  		exec("sudo /sbin/dhcpcd virtual$new_if_count",$output,$result);
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
                global $ipa, $mask, $GW;
                sleep(3);
                exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$2}'| grep -v ^\"169.254.\" |grep [0-9]",$output1,$result);
                exec("sudo /sbin/ifconfig virtual$new_if_count| grep -i inet| grep -i netmask|awk -F \" \" '{print$4}'|grep [0-9]",$output4,$result);
                exec("sudo /sbin/route -n| grep -i virtual$new_if_count|awk -F \" \" '{print$2}'|grep -v \"0.0.0.0\"|grep [0-9]",$output5,$result);

                $ipa=$output1[0];
                $mask=$output4[0];
                $GW=$output5[0];

		$ipcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$ipa,$out);
                sleep(3);

                $ipcheckloop++;

        }

        if ($ipcheck == 0)
        {
                exec("sudo /bin/ip link del virtual$new_if_count",$output7,$result7);
                echo "<script>
                alert('No DHCP lease. Try again or configure static IP address.');
                window.location.href='add-client-decoys.php';
                </script>";
                exit();

        }      


        $networkaddcheck=0;
        while($networkaddcheck == 0)
        {
                global $cidrmask, $networkadd, $networkaddcheck;
                sleep(3);
                exec("sudo /usr/bin/sipcalc virtual$new_if_count|grep -i \"Network mask \"|grep -i \"bits\" |head -1|awk -F \"- \" '{print$2}'|xargs",$outputcidrmask,$result);
                exec("sudo /usr/bin/sipcalc virtual$new_if_count|grep -i \"Network address\"| head -1|awk -F \"- \" '{print$2}'",$outputnetworkadd,$result);

                $cidrmask=$outputcidrmask[0];
                $networkadd=$outputnetworkadd[0];
                $networkaddcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$networkadd,$out);
        }

// Adding policy based routing table
         exec("sudo /bin/ip route flush $new_if_count",$outputflush,$result);
         exec("sudo /bin/ip route add table $new_if_count to $networkadd/$cidrmask dev virtual$new_if_count",$outputadd1,$result);
         exec("sudo /bin/ip route add table $new_if_count to default via $GW dev virtual$new_if_count",$outputadd2,$result);
         exec("sudo /bin/ip rule add from $ipa/32 table $new_if_count priority 11",$outputadd4,$result);
         exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$outputdel1,$result);


// Modify TTL based on provided input

        if (!empty($ttl))
        {
        exec("sudo /sbin/iptables -t mangle -A POSTROUTING -s $ipa -j TTL --ttl-set $ttl",$outputttl,$resultttl);
        }
// Restoring DNS configuration

        exec('sudo /bin/cp -f /etc/resolv.conf.bkp /etc/resolv.conf',$outputdns1,$resultdns1);



  	$mysqli = db_connect();

  	if ($stmt = $mysqli->prepare("INSERT INTO decoys (decoyname,decoygroup,services,interface,virinterface,ip_type,ipaddress,subnet,gateway,macaddress,ttl,decoy_type) VALUES (?, ?, '', ?, ?, ?, ?, ?, ?, ?, ?, ?)"));
    {
    	$stmt->bind_param("sssssssssss", $decoyname,$decoygroup,$interface,$newif,$ip_type,$ipa,$mask,$GW,$mac,$ttl,$decoy_type);
    	$newif="virtual".$new_if_count;
    	$stmt->execute(); 
    	$stmt->close();
    }


  	if ($NBNSCLIENT == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                          exec("sudo /usr/bin/docker run -d --name $decoyname\"_nbnsclient\" --memory=\"256m\" nbnsclient",$outputnbnsclient2,$resultnbnsclient2);
                          sleep (3);
  			exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$output,$result);
                  }
  		exec("sudo /bin/sh /var/log/data/pipework.sh --direct-phys virtual$new_if_count $decoyname\"_nbnsclient\" $ipa/$mask@$GW",$nbnsclientoutput,$result);
                  sleep (3);
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

                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"nbnsclient\" \"$outputnbnsclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$nbnsclientlog,$result);
          }

	if ($SSDPCLIENT == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                        exec("sudo /usr/bin/docker run -d --name $decoyname\"_ssdpclient\" --memory=\"256m\" ssdpclient",$outputssdpclient2,$resultssdpclient2);
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

                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"ssdpclient\" \"$outputssdpclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$ssdpclientlog,$result);
          }




	if ($EMAILCLIENT == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                        exec("sudo /usr/bin/docker run -d --name $decoyname\"_emailclient\" --memory=\"256m\" emailclient /var/run.sh $imap_email $imap_pass $imap_server $imap_port",$outputemailclient2,$resultemailclient2);
                        exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$output,$result);
			
			$dockeripcheck=0;
                        while ($dockeripcheck == 0)
                        {
                                global $dockeripcheck;
                                exec("sudo /usr/bin/docker inspect $decoyname\"_emailclient\" | grep -iw \"ipaddress\"|head -1|awk -F \"\\\"\" '{print$4}'",$outputemailclient7,$resultemailclient);
                                $dockerip=$outputemailclient7[0];
                                $dockeripcheck=preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',$outputemailclient7[0],$out);

                        }
			sleep (3);
			exec("sudo /sbin/iptables -t mangle -I PREROUTING -i docker0 -s $dockerip -d $imap_server -p tcp --dport $imap_port -j ACCEPT",$outputemailclientout,$resultemailclientout);
                        //Updating policy based routing table
                        exec("sudo /bin/ip rule add from $dockerip/32 table $new_if_count priority 11",$outputadd3,$result);
                        //

                  }
                  sleep (5);
                  exec("sudo /usr/bin/docker inspect $decoyname\"_emailclient\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/tmp\/imap.log/g\"",$outputemailclient9,$resultemailclient);


                  //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'EMAILCLIENT; '),imap_email=?,imap_pass=?,imap_server=?,imap_port=? where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("sssss", $imap_email, $imap_pass, $imap_server, $imap_port, $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'emailclient',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputemailclient9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  //
                            sleep (5);

                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"emailclient\" \"$outputemailclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$emailclientlog,$result);
          }




        if ($ARPMITM == 'on')
          {
                  $dockerip=trim(' ');

                  if (!empty($ipa))
                  {
                        exec("sudo /usr/bin/docker run -d --name $decoyname\"_arpmitm\" --memory=\"256m\" arpmitm /var/run.sh $GW",$outputarpclient2,$resultarpclient2);
                        exec("sudo /sbin/route del default gw $GW dev virtual$new_if_count",$output,$result);
                  }
                  exec("sudo /bin/sh /var/log/data/pipework.sh --direct-phys virtual$new_if_count $decoyname\"_arpmitm\" $ipa/$mask@$GW",$arpclientoutput,$result);
                  sleep (5);
                  exec("sudo /usr/bin/docker inspect $decoyname\"_arpmitm\"| grep -i \"MergedDir\"|awk -F \"\\\"\" '{print$4}'|sed \"s/$/\/tmp\/arpclient.log/g\"",$outputarpclient9,$resultarpclient);


                  //
                  $mysqli = db_connect();

                  $stmt = $mysqli->prepare("UPDATE decoys set services=CONCAT(services,'ARPMITM; ') where decoyname=?");

                  if (!$stmt) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt->bind_param("s", $decoyname);

                  $stmt->execute();

                  $stmt->close();

                  $stmt2 = $mysqli->prepare("INSERT into decoydetails (decoyname,decoyservicename,decoyinternalip,decoyroutetable,decoylogfile) VALUES(?,'arpmitm',?,?,?)");

                  if (!$stmt2) {
                      throw new Exception('Error in preparing statement: ' . $mysqli->error);
                  }

                  $stmt2->bind_param("ssss", $decoyname, $dockerip, $new_if_count, $outputarpclient9[0]);

                  $stmt2->execute();

                  $stmt2->close();

                  //
                            sleep (5);

                  exec("sudo /usr/bin/nohup /bin/sh /etc/log.sh \"arpmitm\" \"$outputarpclient9[0]\" \"$decoyname\" \"$decoygroup\" \"$ipa\" \"$decoy_type\" > /dev/null 2>&1 &",$arpclientlog,$result);
          }




		exec("sudo /sbin/iptables -t nat -I PREROUTING -d $ipa -j LOG",$outputmainlogpid,$resultmainlogpid);
		

  		mysqli_close($conn);
  		exit();

}
?>
