#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import MySQLdb
import MySQLdb.cursors 

class MySQL:
    def __init__(self):
	self.db_conn = MySQLdb.connect (host = "localhost",
                        user = "root",
                        passwd = "bewexos",
                        db = "repu", charset='utf8', use_unicode=True, cursorclass=MySQLdb.cursors.DictCursor)
	self.db_cursor = self.db_conn.cursor()

    def execute(self, sql):
	self.db_cursor.execute(sql)
	return self.db_cursor
 
    def __del__(self):
	self.db_cursor.close()
	self.db_conn.close()
