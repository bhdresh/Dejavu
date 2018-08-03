
## Virtual Box Download Link
- Framework/Tool is published here: [Beta V5](https://drive.google.com/open?id=1DAxqipz6gmVBuULctmmuDAKLdmWXZQy-)
- This is a Beta Release and is being shared for testing and suggestions purpose only. All feedback is welcome.
- Quick setup video guide - https://youtu.be/NOuEGa0241U

## Checksum

**Beta V5**

 - MD5 : 4cfe7f0fb29494915f72232d21cc399b

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
 
