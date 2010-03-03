#!/bin/bash
#
#
#

HTACCESS=.htaccess
DEFS=include/defs.php
ACCESSES=include/access.php

cat << EnD
******************* ATTENTION *********************
You must run this script by command - ./install.sh.

EnD
echo -n 'Do you want directly point the path of site directory? (y/n): ' && read ANS

if [ $ANS == "y" ]; then
	echo -n 'Set absolute path: ' && read BASE
else
	BASE=`pwd`
	cd $BASE
fi

PARSER=$BASE/programs/schedule/parser_pm
sed "2a define\(\'template\',\"$PARSER\/*.txt\",true\)\;\n" $PARSER/parser_txt.template > \
$PARSER/parser_txt.php && chmod +x $PARSER/parser_txt.php

echo "php_value include_path \"$BASE\"" > $HTACCESS

#
#
#
echo -n "Enter base url(without leading http://): " && read URL

BASE_URL=http://$URL

function define_const() {
while [ ! $1 == "" ]; do
	printf "define(%b, %b, true);\n" "$1" "$2"
	shift 2
done
}

function define_hrefs() {
while [ ! $1 == "" ]; do
	define_const "'$1'" "htmlspecialchars('$2')"
	shift 2
done
}

echo "<?php" > $DEFS
echo "<?php" > $ACCESSES

define_hrefs	base_url $BASE_URL \
		about_url $BASE_URL/about \
		info_url $BASE_URL/info \
		stud_url $BASE_URL/stud \
		work_url $BASE_URL/work \
		news_url $BASE_URL/news \
		lib_url	 $BASE_URL/library \
		forum_url $BASE_URL/forum \
		gallary_url $BASE_URL/gallary \
		images_url $BASE_URL/images \
	        css_style_url $BASE_URL/style.css >> $DEFS

cat << eND >> $ACCESSES
#
# ACCESS MODES [atom]
#
eND
define_const	"'A_FULL'" "0xFFFFFFFF" \
		"'A_ANON_READ'" "0x1" \
		"'A_ADD_BOOK'" "0x2" \
	        "'A_FORUM_WRITE'" "0x4"	>> $ACCESSES
#
cat << eND >> $ACCESSES
#
# ACCESS MODES [users]
#
eND
define_const	"'A_PREPOD'" "A_ANON_READ | A_ADD_BOOK" \
		"'A_STUD'" "A_ANON_READ | A_FORUM_WRITE" >> $ACCESSES
#

#PHP=$HOME/local/php5/bin/php
#
# Database credentials
#
echo -n 'Enter database user: ' && read USER
echo -n 'Enter user password: ' && read -s PASS && echo
echo -n 'Enter database name: ' && read DBNAME

DBPARAM="user=$USER password=$PASS dbname=$DBNAME"

cat << eND >> $DEFS
#
# Database credentials
#
eND

printf "define('%b', '%b', true);\n" db_profile "$DBPARAM" >> $DEFS

echo "?>" >> $DEFS
echo "?>" >> $ACCESSES

#
# Install sql-files
#
FILES=`find ./sql -type f -name \*.sql | sort`

echo 'DROP LANGUAGE IF EXISTS plPGSQL CASCADE; CREATE LANGUAGE plPGSQL;' |
cat - $FILES | psql -U $USER $DBNAME
php $PARSER/parser_txt.php

#
# Просто для массовости :)
#
cp $BASE/info/photo/*.jpg $BASE/gallary/gallery/

#
# Файл с отзывами и предложениями
#

touch $BASE/about/content.txt
chmod o+w $BASE/about/content.txt
