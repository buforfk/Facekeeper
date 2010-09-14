#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入需要的 Library
import gearman,os, datetime, yaml

def backup(job):
    config_text = open("/var/www/Facekeeper/config/database.yml")

    ENV = "development"

    config  = yaml.load(config_text)
  
    command = "mysqldump -u" + config[ENV]["user"]+" -p"+config[ENV]["password"]+" "+config[ENV]["name"] +" | bzip2 -9f > /var/www/Facekeeper/backup/" + datetime.datetime.today().strftime("%Y%m%d%H%M%S") + ".bz2"
    print command
    os.system(command)

def delete_backup(job):
    job_content = eval(job.arg)

    command = "rm -f /var/www/Facekeeper/backup/" + job_content["file"]
    os.system(command)
    

worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_function("system_backup", backup)
worker.register_function("delete_backup", delete_backup)
worker.work()
