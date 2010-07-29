#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入需要的 Library
import gearman, FK_Common, FK_FB

# set up a grabber object
grabber = FK_FB.URLGenerater(FK_FB.Group())

worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_function("FB_fetchGroup", grabber.grab)
worker.work()
