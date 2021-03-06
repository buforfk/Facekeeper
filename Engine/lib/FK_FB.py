#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.client, urllib, re, db_conn, FK_Foreman, sys, json

class Fans:
    def __init__(self):
        self.type_id = 1

    def generateURL(self, keyword, depth):
        start = (depth - 1) * 10

        if start < 0:
            start = 0

        return "http://www.facebook.com/search/?q=" + urllib.quote(keyword.decode("raw_unicode_escape").encode("utf-8")) + "&o=65&s=" + str(start)

    def parse(self, text):
        pattern = '<td class="UIFullListing_InfoValue UIFullListing_FirstInfoValue"><a href="(.*?)\?ref=search" onclick=".*?">(.*?)</a></td>'
        return re.findall(pattern, text)

class Group:
    def __init__(self):
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
	self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])
        self.db = db_conn.MySQL()
        self.fetcher = fetcher
        self.url_pool = []
        self.config = FK_Foreman.readConfig(self.db)
        self.type_name = ["群組","粉絲"]

    def grab(self, job):
	job_content = json.loads(job.arg)
        
        # 產生 URL
        for keyword in job_content["keyword"]:
            for page in range(int(self.config["fetch.depth"])):
                self.url_pool.append(self.fetcher.generateURL(keyword,(page+1)))

        # 丟到 PHP 處理
	data_string = json.dumps({"url": self.url_pool, "type": self.fetcher.type_id })
        self.gearman_client.submit_job("FB_grabPage", data_string, priority=gearman.PRIORITY_HIGH, background=True)
 
        self.db.execute("INSERT INTO `logs` SET `daemon` = 'FB_FETCH', `message` = 'FB " + self.type_name[self.fetcher.type_id] + " 網址組合完成', `time` = NOW();")

class Parser:
    def __init__(self):
        self.db = db_conn.MySQL()
        self.type_name = ["群組","粉絲"]

    def parse(self, job):
        job_content = json.loads(job.arg)
        
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
        # 分析完成後，寫入資料庫
        self.writeToDB(directory)

        self.db.execute("INSERT INTO `logs` SET `daemon` = 'FB_PARSER', `message` = 'FB " + self.type_name[self.fetcher.type_id] + " 網頁分析完成', `time` = NOW();")

    def writeToDB(self, directory):
        for item in directory:
            self.db.execute( "INSERT INTO `fb_directories` SET `url` = '" + item[0].replace('\\/','/').replace('&amp;','') + "&v=wall', `title` = '" + item[1] + "', `type` = '" + str(self.fetcher.type_id) + "', `tracking` = 0;")           

class Encoder:
    def __init__(self):
        self.db = db_conn.MySQL()
        self.gearman_client = gearman.client.GearmanClient(["127.0.0.1"])

    def encode(self, job):
        job_content = json.loads(job.arg)

        fs = open("/var/www/Facekeeper/tmp/Page_store/"+job_content["pid"]+"/"+job_content["url"]+".html")
        fs_list = fs.readlines()
        fs_text = ''.join(fs_list)
        fs_text = fs_text.decode("raw_unicode_escape").encode("utf-8")
        fs.close()

        fs = open("/var/www/Facekeeper/tmp/Page_store/"+job_content["pid"]+"/"+job_content["url"]+".html","w")
        fs.write(fs_text)
        fs.close()

        self.db.execute("INSERT INTO `logs` SET `daemon` = 'FK_FB:Encoder', `message` = '"+job_content["url"]+" 成功轉換', `time` = NOW();")
	data_string = json.dumps(job_content)

        self.gearman_client.submit_job("matchKeyword", job_content,priority=gearman.PRIORITY_HIGH, background=True)
