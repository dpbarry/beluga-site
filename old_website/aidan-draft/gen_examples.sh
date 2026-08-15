
#!/bin/sh
blank_to_template(){
	name="$1"
	t=`mktemp`
	cat ~/public_html/beluga/aidan-draft/examples/template1 $1 ~/public_html/beluga/aidan-draft/examples/template2 > $t
	mv $t $name
}

cp -r ~/git/beluga/examples/literate_beluga/ ~/public_html/beluga/aidan-draft/examples/
find ~/public_html/beluga/aidan-draft/examples/literate_beluga/ -name "*.bel" -exec ~/git/beluga/bin/beluga +html -css -print {} \;
find ~/public_html/beluga/aidan-draft/examples/literate_beluga/ -name "*.html" | while read arg; do blank_to_template $arg; done
chmod -R 755 ~/public_html/beluga/aidan-draft/examples/literate_beluga/*
python ~/public_html/beluga/aidan-draft/c2.py > ~/public_html/beluga/aidan-draft/casestudies.html
chmod 755 ~/public_html/beluga/aidan-draft/casestudies.html
