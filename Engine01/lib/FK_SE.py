#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman,urllib2,datetime,hashlib,os,sys,FK_SearchEngine, db_conn

class Grabber:
    def __init__(self):
	self.datetime_obj = datetime.datetime
	self.hashobj = hashlib.new('sha1')
	self.gearman_client = gearman.GearmanClient(["127.0.0.1"])
        self.db = db_conn.MySQL()

    def grab(self, job):
	job_content = eval(job.arg)
	
	self.request_obj = urllib2.Request(job_content["url"])
	self.hashobj.update(job_content["url"])
	
        # 設定標頭
        self.setupHeader(job_content["type"])
        # 抓取頁面
	self.getResult()
	# 儲存頁面
	self.saveIntoFile(job_content)
	# throw Ana job
	self.throwAnaJob(job_content)
	# 列印處理完成
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'GRABSEPAGE', `message` = '" + self.hashobj.hexdigest() + "', `time` = NOW();")

    def setupHeader(self, j_type):
	if j_type == 'Yahoo':
	    self.request_obj.add_header("Accept-Language","zh-TW")
	self.request_obj.add_header("Accept","application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5")
	self.request_obj.add_header("Accept-Charset","ISO-8859-1,utf-8;q=0.7,*;q=0.7")
	self.request_obj.add_header("User-Agent","Mozilla/5.0 (X11; U; Linux x86_64; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.38 Safari/533.4")

    def getResult(self):
	self.rs = urllib2.urlopen(self.request_obj).read()

    def saveIntoFile(self, job):
        path = os.path.realpath("../tmp/SE_store/"+job["pid"])
        
        if not os.path.isdir(path):
	    os.mkdir(path)
	
        fs = open(path + "/" + self.hashobj.hexdigest() + ".html","w+")
	
	fs.write(self.rs)
	fs.close()

    def throwAnaJob(self, job):
	self.gearman_client = gearman.GearmanClient(["127.0.0.1"])		
	self.gearman_client.dispatch_background_task("parseSEPage", {"pid": job["pid"] , "url": self.hashobj.hexdigest(), "type": job["type"]})

# Parser
# 分析器
class Parser:
    def __init__(self, db):
	self.db = db
        self.datetime_obj = datetime.datetime
	self.gearman_client = gearman.GearmanClient(["127.0.0.1"])

    def parse(self, job):
	job_content = eval(job.arg)

	f = open("../tmp/SE_store/" + job_content["pid"] + "/" + job_content["url"] + ".html")
	fs = f.readlines()
	file_text = ''.join(fs);
        SE = self.chooseSE(job_content["type"])
        
        if job_content["type"] == "Youtube":
            self.writeTubeDB(job_content["pid"], SE.parse(file_text))
	elif SE != None:
	    self.writeToDB(job_content["pid"], SE.parse(file_text))
    
    def chooseSE(self, se_name):
	
	if se_name == "Google":
            return FK_SearchEngine.Google()

	if se_name == "Yahoo":
            return FK_SearchEngine.Yahoo()

        if se_name == "Bing":
            return FK_SearchEngine.Bing()

        if se_name == "Youtube":
            return FK_SearchEngine.Youtube()

    def writeToDB(self, pid, webpage_links):
        
	for link in webpage_links:
            
            self.db.execute("INSERT INTO `result_pool` SET `pid` = '" + pid + "',`hash` = SHA1('" + link[0] + "'),`url` = '" + link[0] + "', `title` = '" + link[1] + "', `time` = NOW();")
            
            result_temp = self.db.execute("SELECT LAST_INSERT_ID();").fetchone()
            
            result_temp2 = self.db.execute("SELECT `hash` FROM `result_pool` WHERE `id` = '" + str(result_temp["LAST_INSERT_ID()"]) + "'").fetchone()


            self.gearman_client.dispatch_background_task("saveSELink", {"pid": str(pid), "id": str(result_temp["LAST_INSERT_ID()"])}) 
	    self.gearman_client.dispatch_background_task("grabPage", {"pid": str(pid), "url": link[0] ,"hash": result_temp2["hash"] }) 

            self.db.execute("INSERT INTO `logs` SET `daemon` = 'PARSESEPAGE', `message` = '" + str(link[0]) + "(" + str(result_temp["LAST_INSERT_ID()"]) + ")" + "', `time` = NOW();")

    def writeTubeDB(self, pid, webpage_links):
        for link in webpage_links:
            
            self.db.execute("INSERT INTO `youtube_pool` SET `pid` = '" + pid + "',`hash` = SHA1('" + link[0] + "'),`url` = '" + link[0] + "', `title` = '" + link[1] + "', `time` = NOW();")
            
            result_temp = self.db.execute("SELECT LAST_INSERT_ID();").fetchone()
            
	    self.gearman_client.dispatch_background_task("grabVidInfo", {"pid": str(pid), "url": link[0] ,"id": str(result_temp["LAST_INSERT_ID()"]) }) 

            self.db.execute("INSERT INTO `logs` SET `daemon` = 'PARSESEPAGE', `message` = 'Youtube: " + str(link[0]) + "(" + str(result_temp["LAST_INSERT_ID()"]) + ")" + "', `time` = NOW();")
