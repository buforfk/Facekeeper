#!/bin/bash

#
# Facekeeper Booter by bu <bu@hax4.in> 2010
#

#
# Change this if required
#
export FacekeeperPath="/var/www/Facekeeper"

#
# Include functions
#
source "$FacekeeperPath/script/boot_func.sh"

#
# Check Gearman Status
#

GearmanStatus=$(ps aux | grep gearman | wc -l)

if [ "$GearmanStatus" == "1" ]; then
	LogIt "Daemon,Gearman,0"
	sudo /etc/init.d/gearman-job-server start
	LogIt "Daemon,Gearman,2"
else
	LogIt "Daemon,Gearman,1"
fi

#
# check if workers alive
#
checkWorker "grabSEPage" 
checkWorker "parseSEPage"
checkWorker "saveSELink"
checkWorker "grabPage"
checkWorker "encodePage"
checkWorker "matchKeyword"
checkWorker "FK_Backup"
checkWorker "FForeman"
checkWorker "grabVidInfo"
checkWorker "FB_fetchFans"
checkWorker "FB_fetchGroup"
checkWorker "FB_parsePage"
checkWorker "FB_grabPage"
checkWorker "FB_encodePage"

#
# Check Last run(if exist), compared with config/engine_interval, if greater than, php throwForeman.php
# if not exist last run, php throwForeman.php

if [ ! -e "$FacekeeperPath/tmp/engine.lastrun" ]; then
	LogIt "Engine,Foreman,-1"
	
	touch "$FacekeeperPath/tmp/engine.lastrun"
	date '+%s' >> "$FacekeeperPath/tmp/engine.lastrun"

	php "$FacekeeperPath/Engine/throwForeman.php"
	LogIt "Engine,Foreman,1"
else
	currentTick=$(cat "$FacekeeperPath/tmp/engine.lastrun")
	interval=$(cat "$FacekeeperPath/config/engine.interval")
	targetTick=$[$currentTick+$interval]
	currentTime=$(date '+%s')

	if [ $currentTime -ge $targetTick ]; then
		# Execute it and put lastrun in file
		php "$FacekeeperPath/Engine/throwForeman.php"
		LogIt "Engine,Foreman,1"
		date '+%s' > "$FaceekeeperPath/tmp/engine.lastrun"

	else
		remainTime=$[targetTick-currentTime]

		LogIt "Engine,Foreman,0,C:$currentTime;T:$targetTick;R:$remainTime"
	fi
fi

#
# Clean up Temp files
#
clearExpiredData

#
# Put A Seperate Line
#
LogIt "=================================="




