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
