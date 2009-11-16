#!/bin/sh
#
# $1 - username
# $2 - db
#

function remove_tables() {
	while [[ $1 != "" ]]; do
		(echo USE $DB_NAME; echo DROP TABLE $1) |
		mysql -u "$USER" --password="$pass";
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
else
	DB_NAME=$2;
fi

echo -n 'Enter password: '
read -s pass;
echo

shift 2

remove_tables $@

for sqlfile in `find -name '*.sql' -perm -04 | sort`; do
	echo -n Runinig file $sqlfile ... > `tty`
	(echo "USE $DB_NAME;"; cat $sqlfile) |
	mysql -u "$USER" --password="$pass" &&
	echo 'Success!!!' ||
       	echo 'Failed'
done;

#
# Common login file
#
echo Generate logins.php file ...
echo "<?php define('dbuser', \"$USER\", true);" \
     "define('dbpassword', \"$pass\", true);" \
     "define('dbname',\"$DB_NAME\", true); ?>" > include/logins.php;

