#!/bin/bash

#
# Facekeeper Booter Functions
# by bu <bu@hax4.in> 2010
#

#
# Logger Function
#

function LogIt 
{
	test -e "$FacekeeperPath/tmp/Booter.log" || touch "$FacekeeperPath/tmp/Booter.log"
	echo -n $(date '+%Y-%m-%d,%H:%M:%S') >> "$FacekeeperPath/tmp/Booter.log"
	echo ",$1" >> "$FacekeeperPath/tmp/Booter.log"
}

#
# Check Worker Status Function
#

function checkWorker
{
	WorkerStatus=$(ps aux | grep "$1" | wc -l)
	
	if [ "$WorkerStatus" == "1" ]; then
		LogIt "Worker,$1,0"
		python "$FacekeeperPath/Engine/$1.py" &
	else
		LogIt "Worker,$1,1"
	fi
}

#
# clearExpiredData
#
function clearExpiredData
{
	# SE_store (only recent 20)
	ls $FacekeeperPath/tmp/SE_store/ | sort -V | head -n -20 | awk -v FacekeeperPath="$FacekeeperPath" '{ print  FacekeeperPath "/tmp/SE_store/"$1}' | xargs rm -rf
	LogIt "clearExpiredData,SE,1"

	# Page_store (only recent 50)
	ls $FacekeeperPath/tmp/Page_store/ | sort -V | head -n -50 | awk -v FacekeeperPath="$FacekeeperPath" '{ print  FacekeeperPath "/tmp/Page_store/"$1}' | xargs rm -rf
	LogIt "clearExpiredData,Page,1"

	# Database Record (only recent 50)
	php $FacekeeperPath/script/clearExpiredRecords.php

	. $FacekeeperPath/script/removeTemplateCache.sh
}
