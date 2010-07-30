#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入必要的 Library
import gearman, sys, os, hashlib

import FK_SearchEngine

ideal_works = ["grabSEPage", "parseSEPage","saveSELink","grabPage","encodePage","matchKeyword", "grabVidInfo","FB_fetchFans","FB_fetchGroup","FB_parsePage","FB_grabPage","FB_encodePage"]

# readConfig
# 讀取存在資料庫的設定值，並把他們變成一個 dict 方便存取
def readConfig(db):
    FK_CONFIGS_RAW = db.execute("SELECT * FROM `configs` ORDER BY `name`;").fetchall()

    FK_CONFIGS = {}

    for CONFIG in FK_CONFIGS_RAW:
        FK_CONFIGS[CONFIG["name"]] = CONFIG["value"]
    
    return FK_CONFIGS

# callWorkers
# 查看看目前的工人是不是都醒來了
def callWorkers():
    works = []
    
    manager = gearman.manager.GearmanManager("127.0.0.1")

    for worker in manager.workers():
        if len(worker["functions"]) > 0:
            if len(worker["functions"]) > 1:
                for work in worker["functions"]:
                    works.append(work)  
            else:
                works.append(worker["functions"][0])
        
    # 把工作清單整理一下
    works.sort()
    ideal_works.sort()

    if works == ideal_works:
        print "good"
    else:
        for work in works:
            try:
                ideal_works.remove(work)
            except Exception:
                pass;
        
        for work in ideal_works:
            print work
            os.system("python /var/www/Facekeeper/Engine/"+work+".py &")


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

    # 開始進行處理
    try:
        process_obj.process(depth)

    # 如果 Gearman 沒開
    except RuntimeError:
        print "請啟動Gearman"
        os.system("sudo /etc/init.d/gearman-job-server start")
        pass

    callWorkers()
    

class Daemon_Process:

    def __init__(self,db):
	self.db = db
	self.SE = []
        self.gearman_client = gearman.GearmanClient(["127.0.0.1"])	

    def registerCronProcess(self):
	self.db.execute("INSERT INTO `cron_running_logs` SET `start_time` = NOW();")
	pid_temp = self.db.execute("SELECT LAST_INSERT_ID();").fetchone()
	return pid_temp["LAST_INSERT_ID()"]

    def registerSearchEngine(self,SE):
	self.SE.append(SE)

    def listSearchEngine(self):
	print self.SE

    def process(self, depth):
	pid = self.registerCronProcess()
    	web_keywords = self.db.execute("SELECT `keyword` FROM `keywords` WHERE `type` = 1").fetchall()
	youtube_keyword = self.db.execute("SELECT `keyword` FROM `keywords` WHERE`type` = 2").fetchall()
	facebook_pages = self.db.execute("SELECT `title`,`url` FROM `fb_pool`;").fetchall()
        
        # 動態組配的會放在這裡

        # Facebook  
        self.hashobj = hashlib.new('sha1')
        for page in facebook_pages:
            self.hashobj.update(page["url"])
            page_hash = self.hashobj.hexdigest()

            self.db.execute("INSERT INTO `result_pool` SET `pid` = '" + str(pid) + "',`hash` = '" + self.hashobj.hexdigest() + "',`url` = '" + page["url"] + "', `title` = '" + page["title"] + "',`source` = 1, `time` = NOW();")

            self.gearman_client.dispatch_background_task("grabPage",{"pid":str(pid), "url": page["url"], "type": 1, "hash":page_hash})

        for SE in self.SE:
            # 先判斷搜尋引擎是不是 Youtube / PTT / Facebook
            # 他們要用不同的關鍵字組
            if SE.typeName == 'Youtube':
                loop_keyword = youtube_keyword
            else:
                loop_keyword = web_keywords

            # 每一個關鍵字
            for keyword in loop_keyword:
                # 每一頁的深度
		for i in range(int(depth)):
                    # 試著去 throw Task
		    try:
                        self.gearman_client.dispatch_background_task("grabSEPage", {"pid": str(pid), "url": SE.generateURL(keyword, i + 1), "type": SE.typeName})
                    # 如果有抓到例外情形，就回傳 RuntimeError 說明
                    except gearman.client.GearmanBaseClient.ServerUnavailable:
                        raise RuntimeError()
