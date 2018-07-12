
## Virtual Box Download Link
- Framework/Tool is published here: [Beta V4](https://drive.google.com/open?id=1vXgwA5tLjwKmACPzxRKV1Kh272U92-6E)
- This is a Beta Release and is being shared for testing and suggestions purpose only. All feedback is welcome.
- Quick setup video guide - https://youtu.be/NOuEGa0241U

## Checksums

**Beta V3**

 - MD5 : 89e2fccb6dd783b5940afbecb1b0fe6d

# DejaVU - Open Source Deception Framework

Deception techniques if deployed well can be very effective for organizations to improve network defense and can be a useful arsenal for blue teams to detect attacks at very early stage of cyber kill chain. But the challenge we have seen is deploying, managing and administering decoys across large networks is still not easy and becomes complex for defenders to manage this over time. Although there are a lot of commercial tools in this space, we haven’t come across open source tools which can achieve this.

With this in mind, we have developed DejaVu which is an open source deception framework which can be used to deploys across the infrastructure. This could be used by the defender to deploy multiple interactive decoys (HTTP Servers, SQL, SMB, FTP, SSH, client side – NBNS) strategically across their network on different VLAN’s. To ease the management of decoys, we have built a web based platform which can be used to deploy, administer and configure all the decoys effectively from a centralized console. Logging and alerting dashboard displays detailed information about the alerts generated and can be further configured on how these alerts should be handled. If certain IP’s like in-house vulnerability scanner, SCCM etc. needs to be whitelisted, this can be configured which effectively would mean very few false positives.

Alerts only occur when an adversary is engaged with the decoy, so now when the attacker touches the decoy during reconnaissance or performs authentication attempts this raises a high accuracy alert which should be investigated by the defense. Decoys can also be placed on the client VLAN’s to detect client side attacks such as responder/LLMNR attacks using client side decoys. Additionally, common attacks which the adversary uses to compromise such as abusing Tomcat/SQL server for initial foothold can be deployed as decoys, luring the attacker and enabling detection.

Video demo for tool is published here: [Youtube URL](https://www.youtube.com/channel/UCXN2ueUF_gaahy1FW_OtKaw)

## Architecture
![Deja Vu Architecture](https://github.com/bhdresh/Dejavu/blob/master/DejaVu_Architecture.png)
 - **Host OS:** Primary OS hosting the DejaVU virtual box. Note: Primary   
   host can be OS independent Windows/Linux and can be based on      
   corporate hardening guidelines.
 - **DejaVu Virtual Box:** Debian based image containing open source deception framework to deploy multiple interactive decoys (HTTP Servers, SQL, SMB, FTP, SSH, client side – NBNS).
 - **Networking**
	 - **Management Interface –** An interface to access web based management console. (Recommended to be isolated from internal network.)
	 - **Decoy Interface –** Trunk/Access interface for inbound connections from different networks towards the interactive decoys. (Recommended to block outbound connections from this interface)
	 - **Virtual Interfaces –** Interfaces bridged with decoy interface to channel traffic towards the decoys. 
- **Server Dockers –** Docker based service containers – HTTP(Tomcat/Apache), SQL, SMB, FTP, SSH
- **Client Dockers –** Docker based client container – NBNS client
- **Management Console (Web + DB) –** A centralized console to deploy, administer and configure all the decoys effectively along with logging and alerting dashboard to display detailed information about the alerts generated.

## Usage Guide

**Initial Setup**

- Quick setup video guide - https://youtu.be/NOuEGa0241U
- Command-line interface credentials - administrator:changepassword

**Add Server Decoy**

1.   To add a decoy, we first need to add a VLAN on which we want to later deploy Decoys.
	 -  Select Decoy Management -> Add VLAN
	 -  Enter the VLAN ID. Use the “List Available VLANs” option to list the VLANs tagged on the interface.
<img src="https://github.com/bhdresh/Dejavu/blob/master/addVLAN.png" width="35%" height="200px"> 

2.   To add server decoy :
	 -  Select Decoy Management ->Add Server Decoy
	 -  Provide the details for new decoy as shown below. Select the services (SMB/FTP/MySQL/FTP/Web Server/SSH) to be deployed, use dynamic or provide a static IP address.
<img src="https://github.com/bhdresh/Dejavu/blob/master/addServerDecoy.png" width="35%" height="200px"> 

3.   Let’s do some port scan's + Auth attempts from attacker machine on server VLAN and analyze the alerts
<img src="https://github.com/bhdresh/Dejavu/blob/master/attackServer.png" width="50%" height="200px"> 

4.   View the alerts triggered when the attacker scanned our decoy and tried to authenticate.
	 -  Select Log Management -> List Events
<img src="https://github.com/bhdresh/Dejavu/blob/master/alertsDashboard.png" width="70%" height="200px"> 

**Add Client Decoy**

1.   To add Client Decoy
	 -   Select Decoy Management ->Add Client Decoy
	 -   Provide the details for new decoy as shown below. It’s recommended to place the client decoy on user VLANs to detect responder/LLMNR attacks.
<img src="https://github.com/bhdresh/Dejavu/blob/master/addClientDecoy.png" width="35%" height="200px"> 

2.   Let’s run responder from attacker machine on end user VLAN and analyze the alerts
<img src="https://github.com/bhdresh/Dejavu/blob/master/responderExample.png" width="50%" height="200px"> 

3. View the alerts triggered when the attacker scanned our decoy and tried to authenticated.
	 -   Log management -> List Events
<img src="https://github.com/bhdresh/Dejavu/blob/master/alertsDashboard2.png" width="70%" height="200px"> 

**Filter Alerts**

1.    Alerts can be configured based on various parameters.  Example – Don’t send alerts from IP – 10.1.10.101. If certain IP’s like in-house vulnerability scanner, SCCM etc. needs to be whitelisted. 
<img src="https://github.com/bhdresh/Dejavu/blob/master/alertManage.png" width="50%" height="300px"> 

## To Do
- [x] Initial Setup Wizard
- [x] Upload custom file structure/pages for web (Apache) decoys
- [x] Persistance on reboot
- [ ] Backup/Restore configuration
- [ ] Code Cleanup and sanitization
- [ ] Add client side decoys generating HTTP, FTP traffic
- [ ] ISO image   
- [ ] Wiki

## Authors
Bhadresh Patel (@bhdresh)

Harish Ramadoss (@hramados)

## Credits

 - AdminLTE for their awesome UI theme
 - Big shout to open source community for previous work on Honeypots/Deception stuff
 
