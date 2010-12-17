#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.worker, db_conn, FK_Common, FK_Page

db = db_conn.MySQL()
Parser = FK_Page.Parser(db)

worker = gearman.worker.GearmanWorker(["127.0.0.1"])
worker.register_task("matchKeyword", Parser.parse)
worker.work()
