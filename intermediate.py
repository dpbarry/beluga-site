#!/usr/bin/env python
# -*- coding: UTF-8 -*-

# enable debugging
import cgitb
import cgi
import urllib
import os
cgitb.enable()

rootdir = "./examples/literate_beluga/"
if not os.path.isdir(rootdir):
  exit(1)

count = 0
dircount = 0
lst = []
dirlst= []

def recurse(root):
    global count
    global dircount

    dirs = filter(lambda x: os.path.isdir(root+"/"+x), os.listdir(root))
    files = filter(lambda x: not os.path.isdir(root+"/"+x), os.listdir(root))
    files = filter(lambda x: ".html" in x, files)
    dirs.sort() 

    for dir in dirs:
        dirlst.append(root + "/" + dir)
        print "<li><a href=\"{}.html\">{}</a></li>".format(str(dir), dir.replace("_", "").replace("0", "").replace("1", "").replace("2", ""))
        print "<ul class=\"nav nav-list tree\">"
        recurse(root+"/"+dir)
        print "</ul></li>"
    dircount += 1

print """
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Le styles -->
<link href="css/bootstrap.css" rel="stylesheet">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
<script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/iframe-auto-height.js"></script>
<script>
  function fix(){
  $('iframe').iframeAutoHeight({minHeight: 50);}
</script>
</head>

<body>


    <div>
	<iframe width="1250px" height="50px" scrolling="no" frameBorder="0" src="header.html"></iframe> 
    </div>"""

print """
        <div class="row">
            <div class="col-sm-2">
                <div class="well">
                    <div>
                        <ul class="nav nav-list">"""

recurse(rootdir)

print "</ul>"

# count = 0
# lst = os.listdir("./case-studies")
# lst.sort()
# for x in lst:
#     if x.endswith(".html"):
#         print """
#         <li class="case-tab"><a href="#ex-""" + str(count) + """"
#         data-toggle="tab">""" + x.split(".html")[0].replace("_", " ") + """</a></li>"""
#         count += 1

# print """
#       </ul>
#       <div id="my-tab-content" class="tab-content">"""

print """
    </div>
  </div>
</div> <!-- end div col-sm-2 -->
<div class="container theme-showcase" role="main">
<div class="col-sm-10">"""

count = 0
dircount = 0

# for x in lst:
#    if x.endswith(".html"):
#        if count == 0:
#            print """
#           <div class=" example " id="ex-""" + str(count) + """">
#                <iframe src=" """ +  x.replace(" ", "%20") + """ " scrolling="no" frameborder="0" width="100%" ></iframe> 
#            </div>"""
#        else:
#            print """
#                <div class="example hidden" id="ex-""" + str(count) + """"">
#                     <iframe src=" """ + x.replace(" ", "%20") + """ " scrolling="no" frameborder="0" width="100%" ></iframe> 
#                </div>"""
#    count += 1

dirlst.sort()

for x in dirlst:
    if os.path.basename(x) == "1Intermediate":
	print """
        	<div class="dir" id="dir-""" + str(dircount) + """">
            	<div class="container"><h2>""" + os.path.basename(x).replace("_", "").replace("0", "").replace("1", "").replace("2", "") + """</h2>"""
	for file in os.listdir(x):
        	if not os.path.isdir(file) and ".desc" in file:
            		print """
               		<h4><a href=" """ + str(x).replace("//", "/") + """/""" + os.path.splitext(os.path.basename(file))[0] + """.html">""" + os.path.splitext(os.path.basename(file))[0].replace("_", " ") + """</a></h4><p>"""
            		with open(x + "/" + file, 'r') as f:
                		print(f.read())
            		print "</p>"
    	print "</div></div>"
    
    dircount += 1

# print """</div></div></div>
print """     </div>
   </div></div>
   <div id="footer">
     <iframe width="100%" height="100px" scrolling="no" frameBorder="0" src="footer.html"></iframe> 
   </div>

   <script>
   $('iframe').iframeAutoHeight({minHeight: 50);
    </script>
  </div>
</body>
</html>
"""
