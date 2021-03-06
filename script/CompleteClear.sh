#! /bin/bash

# 暫存檔
rm -rf /var/www/Facekeeper/tmp/__Twig*.*
rm -rf /var/www/Facekeeper/tmp/SE_store/*
rm -rf /var/www/Facekeeper/tmp/Page_store/*
rm -rf /var/www/Facekeeper/tmp/FB_Store/*
rm -rf /var/www/Facekeeper/tmp/Report/*
rm -rf /var/www/Facekeeper/Engine/*.pyc
rm -rf /var/www/Facekeeper/Engine/lib/*.pyc
rm -rf /var/www/Facekeeper/backup/*.*
rm -rf /var/www/Facekeeper/tmp/engine.lastrun
rm -rf /var/www/Facekeeper/config/engine.interval

# 資料庫
mysql -uroot -p2o\!0fAceK1ePe2 facekeeper < Clear.sql

# 間隔
touch /var/www/Facekeeper/config/engine.interval
echo 86400 >> /var/www/Facekeeper/config/engine.interval

