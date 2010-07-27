#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入必要的 Library
import FK_Common, db_conn, FK_Foreman ; # FK_Common 是基底類別, db_conn 是資料庫連線類別, FK_Foreman 是本程式處理的 Library
import datetime, FK_SearchEngine, os, gearman.manager 

# 資料庫連線
db = db_conn.MySQL()

# 設定參數
FK_CONFIGS = FK_Foreman.readConfig(db)

# DEBUG 時讓他不要檢查上次跑的時間
#db.execute("TRUNCATE `cron_running_logs`;")

# 上一次是什麼時候跑的呢？
last_run = db.execute("SELECT `start_time` FROM `cron_running_logs` ORDER BY `id` DESC LIMIT 1;").fetchone()

# 如果不是沒跑過
if last_run != None:

    # 計算時間差是否超過所設定的間隔值
    execute_timedelta = datetime.datetime.now() - last_run["start_time"]
    execute_timedelta_seconds = execute_timedelta.days * 86400 + execute_timedelta.seconds
    
    # 如果超過，則再跑一次
    if execute_timedelta_seconds >= int(FK_CONFIGS["fetch.interval"]) * 3600:
	FK_Foreman.throwGrabWork(db, FK_CONFIGS["fetch.depth"]) 

    else:
       db.execute("INSERT INTO `logs` SET `daemon` = 'FOREMAN', `message` = '因為離上次執行的時間還沒有大於間隔值("+str(execute_timedelta_seconds)+" / "+str(int(FK_CONFIGS["fetch.interval"]) * 3600)+")，所以放棄';")

# 如果完全沒跑過
else:
    FK_Foreman.throwGrabWork(db , FK_CONFIGS["fetch.depth"])
