#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入必要的 Library
import gearman, sys, os, hashlib, datetime,json

import FK_SearchEngine

# readConfig
# 讀取存在資料庫的設定值，並把他們變成一個 dict 方便存取
def readConfig(db):
    FK_CONFIGS_RAW = db.execute("SELECT * FROM `configs` ORDER BY `name`;").fetchall()

    FK_CONFIGS = {}

    for CONFIG in FK_CONFIGS_RAW:
        FK_CONFIGS[CONFIG["name"]] = CONFIG["value"]
    
    return FK_CONFIGS

# throwGrabWork
# 丟抓取工作給下面的抓取工人(啟動工作鏈)
def throwGrabWork(db, depth):
    FK_CONFIGS = readConfig(db)
    
    # 初始化工作物件
    process_obj = Daemon_Process(db)

    # 是否在後台啟用了 Yahoo？
    if FK_CONFIGS["source.yahoo.enable"] == "1":
        YahooSE = FK_SearchEngine.Yahoo()
        process_obj.registerSearchEngine(YahooSE)
    
    # 是否在後台啟用了 Google ?
    if FK_CONFIGS["source.google.enable"] == "1":
        GoogleSE = FK_SearchEngine.Google()
        process_obj.registerSearchEngine(GoogleSE) 

    # 是否在後台啟用了 Bing ?
    if FK_CONFIGS["source.bing.enable"] == "1":
        BingSE = FK_SearchEngine.Bing()
        process_obj.registerSearchEngine(BingSE) 

    # 是否在後台啟用了 Youtube ? 
    if FK_CONFIGS["source.youtube.enable"] == "1":
        YoutubeSE = FK_SearchEngine.Youtube()
        process_obj.registerSearchEngine(YoutubeSE)

    process_obj.process(depth)

class Daemon_Process:

    def __init__(self,db):
	self.db = db
	self.SE = []
        self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])	

    def registerCronProcess(self):
	self.db.execute("INSERT INTO `cron_running_logs` SET `start_time` = NOW();")
	pid_temp = self.db.execute("SELECT LAST_INSERT_ID();").fetchone()
	return pid_temp["LAST_INSERT_ID()"]

    def registerSearchEngine(self,SE):
	self.SE.append(SE)

    def listSearchEngine(self):
	print self.SE

    def process(self, depth):

        FK_CONFIGS = readConfig(self.db)

	pid = self.registerCronProcess()
    	web_keywords = self.db.execute("SELECT `keyword` FROM `keywords` WHERE `type` = 1").fetchall()
	youtube_keywords = self.db.execute("SELECT `keyword` FROM `keywords` WHERE `type` = 2").fetchall()
	facebook_pages = self.db.execute("SELECT `title`,`url` FROM `fb_pool`;").fetchall()
        
        # 動態組配的會放在這裡

        # 報表
        # 上一次是什麼時候跑的呢？
        last_run = self.db.execute("SELECT `start_time` FROM `cron_running_logs` WHERE `type` = 'REPORT' ORDER BY `id` DESC LIMIT 1;").fetchone()

        # 如果不是沒跑過
        if last_run != None:

            # 計算時間差是否超過所設定的間隔值
            execute_timedelta = datetime.datetime.now() - last_run["start_time"]
            execute_timedelta_seconds = execute_timedelta.days * 86400 + execute_timedelta.seconds
            
            # 如果超過，則再跑一次
            if execute_timedelta_seconds >= int(FK_CONFIGS["report.interval"]) * 86400:
                if FK_CONFIGS["report.enable"] == "1":
                    os.system("php /var/www/Facekeeper/Engine/generateReport.php")
                    self.db.execute("INSERT INTO `cron_running_logs` SET `start_time` = NOW(),`type` = 'REPORT';")
            else:
               self.db.execute("INSERT INTO `logs` SET `daemon` = 'FOREMAN', `message` = '報表因為離上次執行的時間還沒有大於間隔值("+str(execute_timedelta_seconds)+" / "+str(int(FK_CONFIGS["report.interval"]) * 86400)+")，所以放棄生成', `time` = NOW();")
        else:
            if FK_CONFIGS["report.enable"] == "1":
                os.system("php /var/www/Facekeeper/Engine/generateReport.php")
                self.db.execute("INSERT INTO `cron_running_logs` SET `start_time` = NOW(),`type` = 'REPORT';")

        # Facebook
        if FK_CONFIGS["fb.enable"] == "1":
            self.hashobj = hashlib.new('sha1')

            for page in facebook_pages:
                self.hashobj.update(page["url"])
                page_hash = self.hashobj.hexdigest()

                self.db.execute("INSERT INTO `result_pool` SET `pid` = '" + str(pid) + "',`hash` = '" + self.hashobj.hexdigest() + "',`url` = '" + page["url"] + "', `title` = '" + page["title"] + "',`source` = 1, `time` = NOW();")
		
		data_string = json.dumps({"pid":str(pid), "url": page["url"], "type": 1, "hash":page_hash})
		self.gearman_client.submit_job("grabPage", data_string,priority=gearman.PRIORITY_HIGH, background=True)

        tasks = []
        # 正式給 SE 們工作
        for SE in self.SE:
            # 先判斷搜尋引擎是不是 Youtube / PTT / Facebook
            # 他們要用不同的關鍵字組
            if SE.typeName == 'Youtube':
                loop_keyword = youtube_keywords
            else:
                loop_keyword = web_keywords

            # 每一個關鍵字
            for keyword in loop_keyword:
                # 每一頁的深度
		for i in range(int(depth)):
		    data_string = json.dumps({"pid":str(pid), "url": SE.generateURL(keyword, i + 1), "type": SE.typeName})
		    tasks.append({"task":"grabSEPage","data":data_string})
	
        self.gearman_client.submit_multiple_jobs(tasks, background=True, wait_until_complete=False);
