#!/usr/bin/python
# vim: set fileencoding=UTF-8

import urllib,re

class Core:
    def generateURL(self, keyword, depth):
	keyword = keyword["keyword"].replace("\n", "")
	keyword_comma_count = keyword.count(",") # 是否為人工排序之關鍵字？
	
	if keyword_comma_count == 0:
	    return self.singleAttrURL(keyword, depth)
	else:
	    return self.singleAttrURL(keyword.replace(",", " AND "), depth)

class Yahoo(Core):
    def __init__(self):
	self.typeName = "Yahoo"	

    def singleAttrURL(self, keyword, start):
	return "http://tw.search.yahoo.com/search?" + urllib.urlencode({"p": keyword.encode("utf-8")}) + "&ei=utf-8&b=" + str((start -1) * 10 + 1)

    def parse(self, txt):
        pattern = '<div class="res"><div><h3><a .*? href=".*?\*\*(.*?)".*?>(.*?)</a></h3></div>'
        fs = re.findall(pattern, txt)
        fs2 = []
        for fs_site in fs:
            fs2.append([urllib.unquote(fs_site[0]),fs_site[1]])
        return fs2

class Google(Core):
    def __init__(self):
        self.typeName = "Google"
    
    def singleAttrURL(self, keyword, start):
	return "http://www.google.com/search?rls=zh-tw&" + urllib.urlencode({"q": "+" + keyword.encode("utf-8")})   + "&ie=utf-8&oe=utf-8&tbo=1&tbs=qdr:m&start=" + str((start - 1) * 10);
    
    def parse(self, txt):
	pattern = '<h3 class="r"><a href="([^<]*)" class=l.*?>(.*?)<\/a>'
	fs2 = re.findall(pattern,txt) # 先進行匹配，再排序，後分組
 	return fs2

class Youtube(Core):
    def __init__(self):
        self.typeName = "Youtube"

    def singleAttrURL(self, keyword, start):
        return "http://www.youtube.com/results?search_type=videos&" + urllib.urlencode({"search_query": "+" + keyword.encode("utf-8")}) + "&search_sort=video_date_uploaded&page=" + str(start)
    
    def parse(self, txt):
        pattern = 'id="video-long-title-(.{11})" href=".*?" title="(.*?)"'
        fs2 = re.findall(pattern,txt)
        return fs2

class Bing(Core):
    def __init__(self):
        self.typeName = "Bing"
    def singleAttrURL(self, keyword, start):
        return "http://tw.bing.com/search?" + urllib.urlencode({"q": "+" + keyword.encode("utf-8")})   + "&go=&filt=all&FORM=PORE&first=" + str((start - 1 * 10 + 1));

    def parse(self, txt):
        pattern = '<div class="sb_tlst"><h3><a href="(.*?)" onmousedown=".*?">(.*?)</a></h3></div>'
        fs2 = re.findall(pattern,txt) # 先進行匹配，再排序，後分組
        return fs2
