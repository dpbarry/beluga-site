#!/usr/bin/env python

import sys
import os
import subprocess
import time
import cgitb
cgitb.enable()
from cgi import escape, parse_header

print "Access-Control-Allow-Origin: *"
print "Content-type: text/plain"
print

try:
	cl, _ = parse_header(os.environ["CONTENT_LENGTH"])
except:
	cl=0

file = open("./tmp.bel", "w+")

file.write(sys.stdin.read(int(cl)))

try:
	subprocess.Popen(["../../../git/beluga/bin/beluga tmp.bel 2>&1"], shell=True)
except:
	sys.stdout.write(sys.exc_info())
