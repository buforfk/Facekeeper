#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman, FK_Common, FK_SE

# set up a grabber object
grabber = FK_SE.Grabber()

worker = gearman.GearmanWorker(["127.0.0.1"])
worker.register_function("grabSEPage", grabber.grab)
worker.work()
