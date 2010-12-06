#! /bin/bash

test -e /var/www/Facekeeper/backup || mkdir /var/www/Facekeeper/backup
test -e /var/www/Facekeeper/tmp || mkdir /var/www/Facekeeper/tmp
test -e /var/www/Facekeeper/tmp/SE_store || mkdir /var/www/Facekeeper/tmp/SE_store
test -e /var/www/tmp/Page_store || mkdir /var/www/Facekeeper/tmp/Page_store
test -e /var/www/tmp/FB_Store || mkdir /var/www/Facekeeper/tmp/FB_Store
test -e /var/www/Facekeeper/tmp/Report || mkdir /var/www/Facekeeper/tmp/Report

chmod 0777 /var/www/Facekeeper/backup
chmod 0777 /var/www/Facekeeper/tmp
chmod 0777 /var/www/Facekeeper/tmp/SE_store
chmod 0777 /var/www/Facekeeper/tmp/Page_store
chmod 0777 /var/www/Facekeeper/tmp/FB_Store
chmod 0777 /var/www/Facekeeper/tmp/Report
