
#!/bin/sh
blank_to_template(){
	name="$1"
	t='mktemp'
	cat ~/complogic/website/beluga/examples/template1 $1 ~/complogic/website/beluga/examples/template2 > $t
	mv $t $name
}

cp -r ~/Beluga/examples/literate_beluga/ ~/complogic/website/beluga/examples/
find ~/complogic/website/beluga/examples/literate_beluga/ -name "*.bel" -exec ~/Beluga/bin/beluga +html -css -print {} \;
find ~/complogic/website/beluga/examples/literate_beluga/ -name "*.html" | while read arg; do blank_to_template $arg; done
chmod -R 755 ~/complogic/website/beluga/examples/literate_beluga/*
python ~/complogic/website/beluga/casestudies.py > ~/complogic/website/beluga/casestudies.html
chmod 755 ~/complogic/website/beluga/casestudies.html
python ~/complogic/website/belugRoa/beginner.py > ~/complogic/website/beluga/0Beginner.html
chmod 755 ~/complogic/website/beluga/0Beginner.html
python ~/complogic/website/beluga/intermediate.py > ~/complogic/website/beluga/1Intermediate.html
chmod 755 ~/complogic/website/beluga/1Intermediate.html
python ~/complogic/website/beluga/advanced.py > ~/complogic/website/beluga/2Advanced.html
chmod 755 ~/complogic/website/beluga/2Advanced.html
git add ./casestudies.html ./0Beginner.html ./1Intermediate.html ./2Advanced.html ./examples/literate_beluga/*.html
git commit -m "regenerating case-studies"
git push origin master
