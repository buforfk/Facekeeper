#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman, urllib, re, db_conn, FK_Foreman, sys

class Fans:
    def __init__(self):
        print "Fans()"
        self.type_id = 1

    def generateURL(self, keyword, depth):
        start = (depth - 1) * 10

        if start < 0:
            start = 0

        return "http://www.facebook.com/search/?q=" + urllib.quote(keyword.decode("raw_unicode_escape").encode("utf-8")) + "&o=65&s=" + str(start)

    def parse(self, text):
        pattern = '<td class="UIFullListing_InfoValue UIFullListing_FirstInfoValue"><a href="(.*?)ref=search" onclick=".*?">(.*?)</a></td>'
        return re.findall(pattern, text)

class Group:
    def __init__(self):
        print "Group()"
        self.type_id = 0

    def generateURL(self, keyword, depth):
        start = (depth - 1) * 10

        if start < 0:
            start = 0

        return "http://www.facebook.com/search/?q=" + urllib.quote(keyword.decode("raw_unicode_escape").encode("utf-8")) + "&o=69&s=" + str(start)

    def parse(self, text):
        pattern = '<td class="UIFullListing_InfoValue UIFullListing_FirstInfoValue"><a href="(.*?)ref=search" onclick=".*?">(.*?)</a></td>'
        return re.findall(pattern, text)

class URLGenerater:
    def __init__(self, fetcher):
	self.gearman_client = gearman.GearmanClient(["127.0.0.1"])
        self.db = db_conn.MySQL()
        self.fetcher = fetcher
        self.url_pool = []
        self.config = FK_Foreman.readConfig(self.db)
        self.type_name = ["群組","粉絲"]

    def grab(self, job):
	job_content = eval(job.arg)
        
        # 產生 URL
        for keyword in job_content["keyword"]:
            for page in range(int(self.config["fetch.depth"])):
                self.url_pool.append(self.fetcher.generateURL(keyword,(page+1)))

        # 丟到 PHP 處理
        self.gearman_client.dispatch_background_task("FB_grabPage", {"url": self.url_pool, "type": self.fetcher.type_id })  
 
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'FB_FETCH', `message` = 'FB " + self.type_name[self.fetcher.type_id] + " 網址組合完成', `time` = NOW();")

class Parser:
    def __init__(self):
        self.db = db_conn.MySQL()
        self.type_name = ["群組","粉絲"]

    def parse(self, job):
        job_content = eval(job.arg)
        
        # 叫出對應的 Fetcher
        if job_content["type"] == 0:
            self.fetcher = Group()
        else:
            self.fetcher = Fans()


        # 把暫存檔打開，並分析
        f = open("/var/www/Facekeeper/tmp/FB_Store/" + job_content["hash"] + ".html")
        fs = f.readlines()
        file_text = ''.join(fs).replace('<\/','</').replace('\\\"','"').decode('raw_unicode_escape')
 
        directory = self.fetcher.parse(file_text)
        print directory
        sys.exit()

        # 分析完成後，寫入資料庫
        self.writeToDB(directory)

        self.db.execute("INSERT INTO `logs` SET `daemon` = 'FB_PARSER', `message` = 'FB " + self.type_name[self.fetcher.type_id] + " 網頁分析完成', `time` = NOW();")

    def writeToDB(self, directory):
        for item in directory:
            self.db.execute("INSERT INTO `fb_directories` SET `url` = '"+item["url"]+"', `title` = '"+item["title"]+"', `type` = '"+self.fetcher.type_id+"', `tracking` = 0;")            
