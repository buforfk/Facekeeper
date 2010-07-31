#!/usr/bin/python
# vim: set fileencoding=UTF-8

# Facekeeper v1.0
# (C) 2010 bu <bu@hax4.in>, Zero <mrjjack@hotmail.com>

import gearman

def nothing():
    return 0

ideal_works = ["grabSEPage", "parseSEPage","saveSELink","grabPage","encodePage","matchKeyword", "grabVidInfo","FB_fetchFans","FB_fetchGroup","FB_parsePage","FB_grabPage","FB_encodePage"]

worker = gearman.GearmanWorker(["127.0.0.1"])

for work in ideal_works:
    worker.register_function(work, nothing)

worker.work()
