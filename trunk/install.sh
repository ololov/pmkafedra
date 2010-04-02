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
echo -n "Enter base url(example: http://mysite.localhost:8080/ or just / : " && read URL

if ! echo $URL | grep -q /$ ; then
	URL=$URL/
fi

BASE_URL=$URL

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
		about_url ${BASE_URL}about \
		info_url ${BASE_URL}info \
		stud_url ${BASE_URL}stud \
		work_url ${BASE_URL}work \
		news_url ${BASE_URL}news \
		lib_url	 ${BASE_URL}library \
		forum_url ${BASE_URL}forum \
		gallary_url ${BASE_URL}gallary \
		images_url ${BASE_URL}images \
	        css_style_url ${BASE_URL}style.css >> $DEFS

cat << eND >> $ACCESSES
#
# ACCESS MODES [atom]
#
eND
define_const	"'A_FULL'" "0xFFFFFFFF" \
		"'A_ANON_READ'" "0x1" \
		"'A_ADD_BOOK'" "0x2" \
	        "'A_FORUM_WRITE'" "0x4" \
		"'A_ADD_NEWS'" "0x8"	>> $ACCESSES
#
cat << eND >> $ACCESSES
#
# ACCESS MODES [users]
#
eND
define_const	"'A_PREPOD'" "A_ANON_READ | A_ADD_BOOK | A_ADD_NEWS | A_ADD_FORUM_WRITE" \
		"'A_STUD'" "A_ANON_READ | A_FORUM_WRITE" >> $ACCESSES
#
function passgen() {
alpha=(a b c d e f g h i j k l m n o p q r s t u v w x y z \
	A B C D E F G H I J K L M N O P Q R S T U V W X Y Z \
	0 1 2 3 4 5 6 7 8 9 _)
I=0
while [ ! $I -eq 24 ]; do
	PASS=$PASS${alpha[$(($RANDOM % ${#alpha[@]}))]}
	I=$(($I + 1))
done
}

#
# Database credentials
#
echo -n 'Enter database superuser: ' && read USER
#echo -n 'Enter user password: ' && read -s PASS && echo
echo -n 'Enter initial database: ' && read DBNAME

#DBPARAM="user=$USER password=$PASS dbname=$DBNAME"

unset PASS && passgen
READERPASS="$PASS"
READERDB="user=reader password=$READERPASS dbname=$DBNAME"

unset PASS && passgen
WRITERPASS="$PASS"
WRITERDB="user=writer password=$WRITERPASS dbname=$DBNAME"


cat << eND >> $DEFS
#
# Database credentials
#
eND

printf "define('%b', '%b', true);\n" db_reader "$READERDB" >> $DEFS
printf "define('%b', '%b', true);\n" db_writer "$WRITERDB" >> $DEFS
# for compability with old version
printf "define('%b', '%b', true);\n" db_profile "$WRITERDB" >> $DEFS

echo "?>" >> $DEFS
echo "?>" >> $ACCESSES

#
# Script for prepare installation db
#
function prepare_install() {
cat << eND
CREATE LANGUAGE plPGSQL;
-- DROP DATABASE IF EXISTS pmkafedra;

eND
unset PASS && passgen
echo "CREATE USER pm_admin PASSWORD '$PASS';"
#cat << eND
#CREATE DATABASE pmkafedra OWNER pm_admin ENCODING UTF8;
#eND
}

#
# Script for post installation db
#
function post_install() {
echo "CREATE USER reader PASSWORD '$READERPASS';"
echo "CREATE USER writer PASSWORD '$WRITERPASS';"

echo "GRANT $USER TO writer;"
cat << eND

--GRANT ALL ON books,news,book_authors,book_deps,recs,bd_relation TO writer;
--GRANT SELECT ON disc_tb,prof_disc_tb,dk_tb,workers_tb,
--wd_tb,wdfull_tb,news_types_tb,news_tb,schedule_table, 
--books_tb,ab_tb,db_tb,abfull_tb,dbfull_tb,recs_tb,recs_w_tb,bd_tb
--TO writer;

GRANT SELECT ON disc_tb,prof_disc_tb,dk_tb,workers_tb,
wd_tb,wdfull_tb,news_types_tb,news_tb,schedule_table, 
books_tb,ab_tb,db_tb,abfull_tb,dbfull_tb,recs_tb,recs_w_tb,bd_tb
TO reader;

eND
}

#
# Install sql-files
#
FILES=`find ./sql -type f -name \*.sql | sort`

#echo 'DROP LANGUAGE IF EXISTS plPGSQL CASCADE; CREATE LANGUAGE plPGSQL;' |
prepare_install > temp.install1
post_install > temp.install2
cat temp.install1 $FILES temp.install2 | psql -U $USER $DBNAME

rm temp.install*

php $PARSER/parser_txt.php

chmod og-rwx $DEFS $ACCESS

#
# Просто для массовости :)
#
cp $BASE/info/photo/*.jpg $BASE/gallary/gallery/

#
# Файл с отзывами и предложениями
#

touch $BASE/about/content.txt
chmod o+w $BASE/about/content.txt
