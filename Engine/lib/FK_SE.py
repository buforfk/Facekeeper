#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.client,urllib2,datetime,hashlib,os,sys,FK_SearchEngine, db_conn,json

class Grabber:
    def __init__(self):
	self.datetime_obj = datetime.datetime
	self.hashobj = hashlib.new('sha1')
	self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])
        self.db = db_conn.MySQL()

    def grab(self, job):
	job_content = json.loads(job.arg)
	self.db.execute("insert into `temp` set `data` = '"+job.arg+"';")
	
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
        # 如果沒有 SE_store 則建立
        dirPath = os.path.realpath("/var/www/Facekeeper/tmp/SE_store")

	if not os.path.isdir(dirPath):
	    os.mkdir(dirPath)
        # 如果沒有 SE_store/%pid% 則建立
	path = os.path.realpath("/var/www/Facekeeper/tmp/SE_store/"+job["pid"])
        
        if not os.path.isdir(path):
	    os.mkdir(path)
	# 寫入檔案
        fs = open(path + "/" + self.hashobj.hexdigest() + ".html","w+")
	
	fs.write(self.rs)
	fs.close()

    def throwAnaJob(self, job):
	self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])		
	data_string = json.dumps({"pid": job["pid"] , "url": self.hashobj.hexdigest(), "type": job["type"]})
	self.gearman_client.submit_job("parseSEPage", data_string,priority=gearman.PRIORITY_HIGH, background=True)

# Parser
# 分析器
class Parser:
    def __init__(self, db):
	self.db = db
        self.datetime_obj = datetime.datetime
	self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])

    def parse(self, job):
	job_content = json.loads(job.arg)

	f = open("/var/www/Facekeeper/tmp/SE_store/" + job_content["pid"] + "/" + job_content["url"] + ".html")
	fs = f.readlines()
	file_text = ''.join(fs);
        SE = self.chooseSE(job_content["type"])
 
        result = SE.parse(file_text)

        if len(result) == 0:
            self.db.execute("INSERT INTO `logs` SET `daemon` = 'PARSESEPAGE', `message` = '*******"+job_content["type"]+" 解析結果為零，或許RE出了問題？**********', `time` = NOW();")

        if job_content["type"] == "Youtube":
            self.writeTubeDB(job_content["pid"], result)
	elif SE != None:
	    self.writeToDB(job_content["pid"], result)
    
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

	    data_string = json.dumps({"pid": str(pid), "id": str(result_temp["LAST_INSERT_ID()"])})	
            self.gearman_client.submit_job("saveSELink", data_string, priority=gearman.PRIORITY_HIGH, background=True) 

	    data_string = json.dumps({"pid": str(pid), "url": link[0] ,"hash": result_temp2["hash"], "type": "0" })
	    self.gearman_client.submit_job("grabPage", data_string, priority=gearman.PRIORITY_HIGH, background=True) 

            self.db.execute("INSERT INTO `logs` SET `daemon` = 'PARSESEPAGE', `message` = '" + str(link[0]) + "(" + str(result_temp["LAST_INSERT_ID()"]) + ")" + "', `time` = NOW();")

    def writeTubeDB(self, pid, webpage_links):
        for link in webpage_links:
            
            self.db.execute("INSERT INTO `youtube_pool` SET `pid` = '" + pid + "',`hash` = SHA1('" + link[0] + "'),`url` = '" + link[0] + "', `title` = '" + link[1] + "', `time` = NOW();")
            
            result_temp = self.db.execute("SELECT LAST_INSERT_ID();").fetchone()
 	    
	    data_string = json.dumps({"pid": str(pid), "url": link[0] ,"id": str(result_temp["LAST_INSERT_ID()"]) })           
	    self.gearman_client.submit_job("grabVidInfo", data_string,priority=gearman.PRIORITY_HIGH, background=True)

            self.db.execute("INSERT INTO `logs` SET `daemon` = 'PARSESEPAGE', `message` = 'Youtube: " + str(link[0]) + "(" + str(result_temp["LAST_INSERT_ID()"]) + ")" + "', `time` = NOW();")
