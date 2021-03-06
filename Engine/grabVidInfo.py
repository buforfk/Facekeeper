#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman.worker, FK_Common ,FK_Tube

# set up a grabber object
grabber = FK_Tube.VidGrabber()

worker = gearman.worker.GearmanWorker(["127.0.0.1"])
worker.register_task("grabVidInfo", grabber.grab)
worker.work()
