#!/bin/sh
#
# $1 - username
# $2 - db
#

if [[ $1 == "" ]]; then
	echo 'You must define username!';
	exit 1;
fi

USER=$1

if [[ $2 == "" ]]; then
	DBNAME=clericsu_pm;
	shift
else
	DBNAME=$2;
	shift 2
fi

echo -n 'Enter password: '
read pass;
echo


#for sqlfile in `find -name '*.sql' -perm -04 | sort`; do
#	echo -n Runinig file $sqlfile ... 
#	cat $sqlfile |psql -U "$USER" $DBNAME
#done;
(echo "CREATE LANGUAGE plPGSQL;"; cat `find -name '*.sql' -perm -04`) |
psql -U "$USER" "$DBNAME"

#
# Common login file
#
loginfile=include/logins.php
#
echo Generate logins.php file ...
#echo -n "<?php define('dbuser', \"$USER\", true); define('dbpassword', \"" > $loginfile
#echo -n $pass | sed "s/\([$\"&]\)/\\\\\\1/g" >> $loginfile
#echo "\", true); define('dbname',\"$DB_NAME\", true); ?>" >> $loginfile
#
echo -n "<?php define('dbparam', 'user=$USER password=$pass dbname=$DBNAME'); ?>" > $loginfile

#
# Генерирование файла .htaccess
#
echo "php_value include_path \"$PWD:.\"" > .htaccess

cd ~/pmkafedra/forum
ln -s ~/pmkafedra/include include
cd ~/pmkafedra/schedule/parser_pm
ln -s ~/pmkafedra/include include
cd
