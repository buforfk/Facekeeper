#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import FK_Common,gearman, db_conn, FK_SE
	
db = db_conn.MySQL()
Parser = FK_SE.Parser(db)

worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_function("parseSEPage", Parser.parse)
worker.work()
