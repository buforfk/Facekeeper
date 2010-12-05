#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入必要的 Library
import FK_Common, db_conn, FK_Foreman ; # FK_Common 是基底類別, db_conn 是資料庫連線類別, FK_Foreman 是本程式處理的 Library
import datetime, FK_SearchEngine, os

def Foreman(job):
    # 資料庫連線
    db = db_conn.MySQL()

    # 設定參數
    FK_CONFIGS = FK_Foreman.readConfig(db)

    FK_Foreman.throwGrabWork(db , FK_CONFIGS["fetch.depth"])

    return true

# set up a grabber object
worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_task("Foreman", Foreman)
worker.work()
