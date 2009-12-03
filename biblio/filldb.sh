#!/bin/sh

if [[ $1 == '' ]]; then
	echo 'User must be specified.'
	exit 1
fi

USER=$1
DBNAME='cleric_pm'

if [[ $2 != '' ]]; then
	DBNAME=$2
fi

CURR=$HOME

if [[ $3 != '' ]]; then
	CURR=$3
fi


YEAR=`date | tail --bytes=5`

addauthor() {
	while [ $1 != '' ]; do
		if [[ $1 == '' ]]; then
			break
		fi
		NAME="'$1'"
		if [[ $2 == '' || $3 == '' || $4 == '' ]]; then
			break
		fi
		AUTHOR="ARRAY['$2 $3 $4']"
		DESC="'$5'"
		PATH="'go/to/space/to/meet/your/face/haha.pdf'"
		DEP="'$6'"
		PUB="'$7'"

		echo "SELECT ADDBOOK($NAME, $AUTHOR, 1, $DESC, $PUB, $YEAR, 'ISBN-32131', 'SCRIPT', $PATH, $DEP, $RANDOM);"
		if [ 7 -gt $# ]; then
			shift $#
		else
			shift 7
		fi
	done
}

addauthor `find $CURR -type d -printf '%f\n' | egrep -i '^[a-z]{2,}$'`|
psql -U "$USER" "$DBNAME"

