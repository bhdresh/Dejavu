# DejaVU - Open Source Deception Platform

DejaVu (part of Camolabs.io) is a deception platform which can be used to deploy decoys on both cloud(for now we support AWS) and internal network. 

This is our presentation at [Blackhat Europe](https://www.youtube.com/watch?v=CtyTs9KbTOU) where we show how we can leverage Deception to detect common adversary tactics and techniques during various stages of attack lifecycle.   

### Deploying DejaVu on AWS 

* Visit  https://cloudengine.camolabs.io/ to get started. Once you have an account,  within a few minutes you can deploy decoys on your **AWS infrastructure**
* [Video Guide](https://youtu.be/_Vz7bRBURNw) to help you get started 

>Note: Configure security group for AWS decoy to only allow traffic from your internal VPC's. This will elliminate noise - putting these decoys on the internet will generate a lot of traffic and defeats the purpose. 

>AMI Image used for the decoy is open-source, you can view it's working if you are curious or want to ensure there is no malicious intent.

### Deploying DejaVu on Internal Network 

If you are looking deploying DejaVu on your internal network, you can download the platform from [Camolabs.io](https://camolabs.io/CAMOLabs/index.html "DejaVu Download"). Use the below guides to help you get started. 

* Setting it up on Virtual Box: [Video Guide](https://youtu.be/FhF6fT8OHjA "Link")
* Setting it up on VMware ESXi (PDF Guides): [Console](https://raw.githubusercontent.com/bhdresh/Dejavu/master/Console_ESXI.pdf "Console") and [Engine](https://raw.githubusercontent.com/bhdresh/Dejavu/master/Engine_ESXI.pdf "Engine")

> Default credentials: administrator:changepassword

# Background

We started DejaVu in 2018 and initially presented our work at [Blackhat](https://www.blackhat.com/us-18/arsenal.html#dejavu-an-open-source-deception-framework), [Defcon](https://www.defcon.org/html/defcon-26/dc-26-demolabs.html#DejaVU), and [HITB](https://conference.hitb.org/hitbsecconf2018dxb/hitb-armory/). Over the last few years we have added various new decoys, breadcrumbs and changed our architecture based on the feedback from organisations using it. 

DejaVu can be used by the defender to deploy multiple interactive (Server and Client) decoys strategically across their network on different VLAN’s and on Cloud (AWS). To ease the management of decoys, we have built a web based platform which can be used to deploy, administer and configure all the decoys effectively from a centralized console. Logging and alerting dashboard displays detailed information about the alerts generated and can be further configured on how these alerts should be handled. If certain IP’s like in-house vulnerability scanner, SCCM etc. needs to be discarded, this can be configured which effectively would mean very few false positives.

Alerts only occur when an adversary is engaged with the decoy, so now when the attacker touches the decoy during reconnaissance or performs authentication attempts this raises a high accuracy alert which should be investigated by the defense. Decoys can also be placed on the client VLAN’s to detect client side attacks such as responder/LLMNR attacks using client side decoys. Additionally, common attacks which the adversary uses to compromise such as abusing Tomcat/SQL server for initial foothold can be deployed as decoys, luring the attacker and enabling detection.

> **One of the major advantages of DejaVu  - Using a single platform you can deploys decoys across different VLANS and manage, monitor them.**

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
