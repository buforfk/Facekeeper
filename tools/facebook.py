#!/usr/bin/python
# vim: set fileencoding=UTF-8

f = open("tmp/facebook_temp.txt")
fs = f.readlines()

fss = ''.join(fs)

fss = fss.decode("raw_unicode_escape").encode("utf-8")

print fss

