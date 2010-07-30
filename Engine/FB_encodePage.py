#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

# 引入需要的 Library
import gearman, FK_Common, FK_FB

# set up a grabber object
encoder = FK_FB.Encoder()

worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_function("FB_encodePage", encoder.encode)
worker.work()
