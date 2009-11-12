#!/bin/sh
#
# $1 - username
#
#

function dummytable() {
cat << eND
USE clericsu_kafedrapm;
CREATE TABLE biblio(id int);
eND
}

function cmdlist() {
cat << eND
USE clericsu_kafedrapm;
DROP TABLE biblio;
CREATE TABLE biblio(
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) CHARSET utf8 NOT NULL,
	volume INT UNSIGNED,
	author VARCHAR(255) CHARSET utf8 NOT NULL,
	description TEXT CHARSET utf8,
	publish VARCHAR(255) CHARSET utf8,
	year INT UNSIGNED,
	isbn VARCHAR(255),
	posted DATETIME NOT NULL,
	who VARCHAR(255) CHARSET utf8 NOT NULL,
	bookpath VARCHAR(255) NOT NULL,
	imgpath VARCHAR(255),
	size INT UNSIGNED NOT NULL,
	pages INT UNSIGNED NOT NULL,
	PRIMARY KEY(id));

INSERT INTO biblio (name, author, description, publish, year,
		    posted, bookpath, imgpath, size, pages, who)
	VALUES ('Book1', 'М. В. Ким', 'Самая лучшая книга в мире',
		'Kims incorp.', 2009, NOW(), 'somewhere/in/galaxy/book1.pdf',
		'somewhere/in/galary/book1.png', 100000, 1024, 'admin'),
		('Book3', 'М. В. Ким, М. В. Ким', 'Самая лучшая книга в мире',
		'Kims incorp.', 2009, NOW(), 'somewhere/in/galaxy/book3.pdf',
		'somewhere/in/galary/book3.png', 200000, 2048, 'admin'),
	       ('Book2', 'И. И. Некто', 'Самая худшая книга в мире',
		'Trash incorp.', 2009, NOW(), 'somewhere/in/trash/book2.pdf',
		'somewhere/in/galary/trash/book2.png', 1000, 102, 'admin');
eND
}

if [[ $1 == "" ]]; then
	echo 'You must define username!';
	exit 1;
fi

echo -n 'Enter password: '
read -s  pass;
echo

# To be sure that table bibio is exist
# Do you have a better solution?
dummytable | mysql -u "$1" --password="$pass" 1> /dev/null 2>/dev/null;

# main work 
cmdlist | mysql -u "$1" --password="$pass" && echo 'Success!!!' || echo 'Failed'


