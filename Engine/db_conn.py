#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.5
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import MySQLdb
import MySQLdb.cursors 
import yaml

class MySQL:
    def __init__(self):
        config_text = open("/var/www/Facekeeper/config/database.yml")
        config = yaml.load(config_text)

        ENV = "production"
	
	self.db_conn = MySQLdb.connect (host = config[ENV]['host'],
                        user = config[ENV]['user'],
                        passwd = config[ENV]['password'],
                        db = config[ENV]['name'], charset='utf8', use_unicode=True, cursorclass=MySQLdb.cursors.DictCursor)
	self.db_cursor = self.db_conn.cursor()

    def execute(self, sql):
	self.db_cursor.execute(sql)
	return self.db_cursor
 
    def __del__(self):
	self.db_cursor.close()
	self.db_conn.close()
