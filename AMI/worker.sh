#!/bin/sh

precheck=`sudo ps auxx| egrep -i "worker.sh|startup.sh"| grep -v "grep"|wc -l`


#change below to "-gt 1" for shc

if [ "$precheck" -gt 3 ] ; then

	echo $precheck > /tmp/b.txt
	sudo ps auxx| egrep -i "worker.sh|startup.sh"| grep -v "grep" >> /tmp/b.txt
exit
fi


cd /var/dejavufiles/engine

deviceid=`sudo mysql -u root dejavu -e "select deviceid,amikey from Users"| grep -iv "deviceid"|awk -F " " '{print$1}'|xargs`
amikey=`sudo mysql -u root dejavu -e "select deviceid,amikey from Users"| grep -iv "deviceid"|awk -F " " '{print$2}'|xargs`

connectivity=`sudo curl -s -I https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php | head -1| grep 200|wc -l|xargs`
	if [ "$connectivity" -eq 1 ] ; then

		rm -f /tmp/delete_command
		sudo curl "https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php?deviceid=$deviceid&ami_key=$amikey&action=delete_command" -o /tmp/delete_command  > /dev/null 2>&1
		delete_command=`cat /tmp/delete_command|grep -i " ###dejavu### "|wc -l|xargs`
		if [ "$delete_command" -eq 1 ] ; then
			docker stop $(docker ps -a -q)
			docker rm $(docker ps -a -q)
			jobid=`cat /tmp/delete_command |awk -F " ###dejavu### " '{print$1}'`
			sudo mysql -u root dejavu -e "delete from decoys"
			sudo mysql -u root dejavu -e "delete from decoydetails"
			ls /var/dejavufiles/uploads/*.zip | egrep -v \"DEFAULTOWA.zip|DEFAULTVPN.zip\"|xargs rm
			sudo curl "https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php?deviceid=$deviceid&ami_key=$amikey&jobid=$jobid&action=deleted"
		fi


		rm -f /tmp/deploy_command
		sudo curl "https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php?deviceid=$deviceid&ami_key=$amikey&action=deploy_command" -o /tmp/deploy_command  > /dev/null 2>&1
		deploy_command=`cat /tmp/deploy_command|grep -i " ###dejavu### "|wc -l|xargs`
		if [ "$deploy_command" -eq 1 ] ; then

			docker stop $(docker ps -a -q)
			docker rm $(docker ps -a -q)
			sudo mysql -u root dejavu -e "delete from decoys"
			sudo mysql -u root dejavu -e "delete from decoydetails"

			downloadfileche=`cat /tmp/deploy_command| awk -F "--smbdecoyfile=|--apachedecoyfile=" '{print$2}' |awk -F " " '{print$1}'|wc -l |xargs`

			if [ "$downloadfileche" -gt 0 ] ; then
				downloadfile=`cat /tmp/deploy_command| awk -F "--smbdecoyfile=|--apachedecoyfile=" '{print$2}' |awk -F " " '{print$1}'|xargs`
				rm -f /var/dejavufiles/uploads/$downloadfile
				sudo curl "https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php?deviceid=$deviceid&ami_key=$amikey&filename=$downloadfile&action=download" -o /var/dejavufiles/uploads/$downloadfile > /dev/null 2>&1
			fi

			jobcommand=`cat /tmp/deploy_command |awk -F " ###dejavu###  " '{print$2}'`
			jobid=`cat /tmp/deploy_command |awk -F " ###dejavu### " '{print$1}'`
			rm -f /tmp/job.sh
			echo "php /var/dejavufiles/engine/add-server-decoys-back.php" "$jobcommand" "--deviceid=$deviceid" >> /tmp/job.sh
			sh /tmp/job.sh
			sudo curl "https://cloudengine.camolabs.io/Decoify/add-aws-ami-check.php?deviceid=$deviceid&ami_key=$amikey&jobid=$jobid&action=deployed"
		fi


	fi

