#!/bin/sh
#
# $1 - username
# $2 - db
#

remove_tables() {
	while [[ $1 != "" ]]; do
		echo DROP TABLE "$1" |
		mysql -u "$USER" --password="$pass" "$DB_NAME";
		shift;
	done;
}

if [[ $1 == "" ]]; then
	echo 'You must define username!';
	exit 1;
fi

USER=$1

if [[ $2 == "" ]]; then
	DB_NAME=clericsu_kafedrapm;
	shift
else
	DB_NAME=$2;
	shift 2
fi

echo -n 'Enter password: '
read -s pass;
echo


remove_tables $@

mysqlopt=--default-character-set=utf8

for sqlfile in `find -name '*.sql' -perm -04 | sort`; do
	echo -n Runinig file $sqlfile ... 
	(echo "USE $DB_NAME;"; echo "charset utf8"; cat $sqlfile) |
	mysql -u "$USER" --password="$pass" $mysqlopt &&
	echo 'Success!!!' ||
       	echo 'Failed'
done;

#
# Common login file
#
loginfile=include/logins.php

echo Generate logins.php file ...
echo -n "<?php define('dbuser', \"$USER\", true); define('dbpassword', \"" > $loginfile
echo -n $pass | sed "s/\([$\"&]\)/\\\\\\1/g" >> $loginfile
echo "\", true); define('dbname',\"$DB_NAME\", true); ?>" >> $loginfile

