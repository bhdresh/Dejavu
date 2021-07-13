# Download 

We started DejaVu in 2018 and initially presented our work at [Blackhat](https://www.blackhat.com/us-18/arsenal.html#dejavu-an-open-source-deception-framework), [Defcon](https://www.defcon.org/html/defcon-26/dc-26-demolabs.html#DejaVU), and [HITB](https://conference.hitb.org/hitbsecconf2018dxb/hitb-armory/). Over the last two years we have added various new decoys, breadcrumbs and changed our architecture based on the feedback from organisations using it. Latest DejaVu platform can be downloaded by using the below link:

Download Preconfigured DejaVu images from here: [Camolabs.io](https://camolabs.io/CAMOLabs/index.html "DejaVu Download")
Default SSH credentials : administrator:changepassword

Quick Setup Guide (Default on Virtual Box): [Link](https://youtu.be/FhF6fT8OHjA "Link")

Setup Guide for VMware ESXi : [Console](https://raw.githubusercontent.com/bhdresh/Dejavu/master/Console_ESXI.pdf "Console") and [Engine](https://raw.githubusercontent.com/bhdresh/Dejavu/master/Engine_ESXI.pdf "Engine")

# DejaVU - Open Source Deception Framework

DejaVu is an open source deception framework which can be used to deploy decoys across the infrastructure. This could be used by the defender to deploy multiple interactive (Server and Client) decoys strategically across their network on different VLAN’s. To ease the management of decoys, we have built a web based platform which can be used to deploy, administer and configure all the decoys effectively from a centralized console. Logging and alerting dashboard displays detailed information about the alerts generated and can be further configured on how these alerts should be handled. If certain IP’s like in-house vulnerability scanner, SCCM etc. needs to be discarded, this can be configured which effectively would mean very few false positives.

Alerts only occur when an adversary is engaged with the decoy, so now when the attacker touches the decoy during reconnaissance or performs authentication attempts this raises a high accuracy alert which should be investigated by the defense. Decoys can also be placed on the client VLAN’s to detect client side attacks such as responder/LLMNR attacks using client side decoys. Additionally, common attacks which the adversary uses to compromise such as abusing Tomcat/SQL server for initial foothold can be deployed as decoys, luring the attacker and enabling detection.

**One of the major advantages of DejaVu is - Using a single platform you can deploys decoys across different VLANS and manage, monitor them.**

### Use Cases

Below are few examples attack vectors using DejaVu platform you can detect:

* (Attack) : Port Scan/Enumeration

  (Defense) : Fake Services spread out throughout the network
  
* (Attack) : Password Spray/ Brute Force Attack

  (Defense) : Deploy multiple common services, attempts on two/more decoys potentially a password spray attempt
  
* (Attack) : Attacker targeting low hanging fruits - Tomcat/MSSQL/Jenkins

  (Defense) : Deploy common platforms attackers look for initial foothold

* (Attack) : Responsder/ LLMNR Poisoning

  (Defense) : NBNS client side decoys to detect MITM attacks

* (Attack) : Bloodhound/Similar tools to identify attack path
  
  (Defense) : DNS Records Manipulation and fake servers

* (Attack) : Lateral Movement - Pass the Hash

  (Defense) : Fake Sessions and Injecting Memory Credentials Tokens

* (Attack) : Kerberoast attack

  (Defense) : Kerberoasting Service Accounts Honey Tokens

* (Attack) : Data Ex-filtration

  (Defense) : Honeyfiles to detect ex-filtration occurrences


# Architecture

[![Architecture](https://raw.githubusercontent.com/bhdresh/Dejavu/master/architecture.png "Architecture")](https://raw.githubusercontent.com/bhdresh/Dejavu/master/architecture.png "Architecture")


 - **DejaVu Engine:** This is used deploy decoys across your infrastrucure. So let's you have multiple offices, you would depoloy an engine in each. 
- **DejaVu Console:** A centralized console to view and manager all the alerts from your various engines. Think of this as your dashboard. Engines connect to Console. 

# Decoy Types

- **Server Decoys**
  - MYSQL
  - SNMP
  - Custom HTTP Decoy - You can configure this with a custom HTML template
  - TELNET
  - SMB Server with custom files
  - FTP
  - TFTP
  - Web Server - Tomcat, Apache, Basic Auth
  - SSH Interactive and Non-Interactive 
  - SMTP
  - RDP Interactive and Non-Interactive 
  - VNC
  - HONEYCOMB (To capture events from Honey Docs)
  - ICS/SCADA Decoys - Modbus and S7COMM

- **Client Decoys**
  - NBNS Decoy
  - MITM Decoy
  - SSDP Client 
  - Email Client

- BreadCrumbs
  - Honey Docs
  - HoneyHash - Injects creds into memory
  - Kerberoast Honey Account
  
# Sneak Peek

<img src="https://raw.githubusercontent.com/bhdresh/Dejavu/master/images/1.png" width="45%" height="250 px"> <img src="https://raw.githubusercontent.com/bhdresh/Dejavu/master/images/2.png" width="45%" height="250 px"> <img src="https://raw.githubusercontent.com/bhdresh/Dejavu/master/images/3.png" width="45%" height="250 px"> <img src="https://raw.githubusercontent.com/bhdresh/Dejavu/master/images/4.png" width="45%" height="250 px">


# Authors
* [Bhadresh Patel](https://twitter.com/bhdresh)
* [Harish Ramadoss](https://twitter.com/hramados)

# Credits
* Big shout to open source community for previous work on Honeypots

