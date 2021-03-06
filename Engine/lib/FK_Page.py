#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.client,urllib2,datetime,hashlib,os,re,itertools,sys, json

class Grabber:
    def __init__(self):
	self.datetime_obj = datetime.datetime

    def grab(self, job):
	job_content = json.loads(job.arg)
	self.request_obj = urllib2.Request(job_content["url"])
	
	# 設定標頭
	self.setupHeader()
	# 抓取頁面
	self.getResult()
	# 儲存頁面
	self.saveIntoFile(job_content)
	# throw Ana job
	self.throwAnaJob(job_content)
	# 列印處理完成
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'GRABPAGE', `message` = '"+job_content["hash"]+"', `time` = NOW();")
    
    # 設定騙人的標頭
    def setupHeader(self):
	self.request_obj.add_header("Accept","application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5")
	self.request_obj.add_header("Accept-Charset","ISO-8859-1,utf-8;q=0.7,*;q=0.7")
	self.request_obj.add_header("User-Agent","Mozilla/5.0 (X11; U; Linux x86_64; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.38 Safari/533.4")

    def getResult(self):
	self.rs = urllib2.urlopen(self.request_obj).read()

    def saveIntoFile(self, job):
        path = os.path.realpath("/var/www/Facekeeper/tmp/Page_store/" + job["pid"])
        
        if not os.path.isdir(path):
	    os.mkdir(path)

	fs = open(path + "/" + job["hash"] + ".html","w+")
	
	fs.write(self.rs)
	fs.close()

    def throwAnaJob(self, job):
	self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])
	
	data_string = json.dumps({"pid": job["pid"] , "url": job["hash"]})	

        if job["type"] == 1:
	    self.gearman_client.submit_jobs("FB_encodePage", data_string,priority=gearman.PRIORITY_HIGH, background=True)
        else:
	    self.gearman_client.submit_jobs("encodePage", data_string,priority=gearman.PRIORITY_HIGH, background=True)

#
#
class Parser:
    def __init__(self,db):
	self.db = db
	keywords = self.db.execute("SELECT `keyword` FROM `keywords`;").fetchall()
	keyword_list = []
	for key in keywords:
	    keyword_list.append(key["keyword"].encode("utf-8"))
	
        pattern = '|'.join(keyword_list)
	self.keyword_pattern = pattern;

    def parse(self,job):
	job_content = json.loads(job.arg)

	f = open("/var/www/Facekeeper/tmp/Page_store/" + job_content["pid"] + "/" + job_content["url"] + ".html")
	fs_list = f.readlines()
	fs = ''.join(fs_list)
        f.close()

        fs2 = itertools.groupby(sorted(re.findall(self.keyword_pattern,fs,re.UNICODE))) # 先進行匹配，再排序，後分組
	keyword_matched = []

	for k,g in fs2:
	    keyword_matched.append(k)

        matched_keywords = ','.join(keyword_matched)
        
        self.db.execute("UPDATE `result_pool` SET `keyword_length` = '" + str(len(keyword_matched)) + "', `keywords` = '" + matched_keywords + "' WHERE `hash` = '" + job_content["url"] + "';")
	
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'matchKeyword', `message` = '" + str(job_content["url"]) + " ("+str(len(keyword_matched))+")', `time` = NOW();")
