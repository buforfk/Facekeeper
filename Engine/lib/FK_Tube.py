#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.client,urllib2,datetime,hashlib,os,re,itertools,sys,db_conn,json

class VidGrabber:
    def __init__(self):
	self.datetime_obj = datetime.datetime
        self.db = db_conn.MySQL()

    def grab(self, job):
	job_content =json.loads(job.arg)

	self.request_obj = urllib2.Request("http://www.youtube.com/watch?v=" + job_content["url"])
	
	# 設定標頭
	self.setupHeader()
	# 抓取頁面
	self.getResult()
	# 儲存頁面
	self.saveIntoFile(job_content)
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
        self.rs = ''.join(self.rs.split())
        
        # date
        rs1 = re.findall('<spanid="eow-date"class="watch-video-date">(.*?)</span>', self.rs)
        rs3 = rs1[0]

        # views
        rs2 = re.findall('<strongclass="watch\-view\-count">(.*?)</strong>', self.rs)
        rs4 = rs2[0]
    
        self.db.execute("UPDATE `youtube_pool` SET `date` = '"+rs3+"', `views` = '"+str(int(rs4))+"' WHERE `id` = '"+str(job["id"])+"';")
        
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'GRABVIDINFO', `message` = '" + str(job["id"]) + "', `time` = NOW();")
