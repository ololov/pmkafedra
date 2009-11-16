#!/bin/sh
#
# $1 - username
#
#

function removetable() {
cat << eND
USE clericsu_kafedrapm;
DROP TABLE biblio;
eND
}

function cmdlist() {
cat << eND
USE clericsu_kafedrapm;
CREATE TABLE biblio(
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	volume INT UNSIGNED,
	author VARCHAR(255) NOT NULL,
	description TEXT,
	publish VARCHAR(255),
	year INT UNSIGNED,
	isbn VARCHAR(255),
	posted DATETIME NOT NULL,
	who VARCHAR(255) NOT NULL,
	bookpath VARCHAR(255) NOT NULL,
	imgpath VARCHAR(255),
	size INT UNSIGNED NOT NULL,
	pages INT UNSIGNED,
	PRIMARY KEY(id)) DEFAULT CHARSET utf8;

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
		'somewhere/in/galary/trash/book2.png', 1000, 102, 'admin'),

	       ('Book4', 'И. Н. Некто',
	        'Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.',
		'Best Trash incorp.', 2003, NOW(),
		'somewhere/in/trash/book4.pdf',
		'somewhere/in/galary/trash/book4.png',
		1000000, 10200, 'admin');
eND
}

if [[ $1 == "" ]]; then
	echo 'You must define username!';
	exit 1;
fi

echo -n 'Enter password: '
read -s pass;
echo

#
# Remove old table
#
removetable | mysql -u "$1" --password="$pass" 1> /dev/null 2>/dev/null;

# main work 
cmdlist | mysql -u "$1" --password="$pass" && echo 'Success!!!' || echo 'Failed'

#
# For correct work of library, it's need file logins.php
# So please run this script into root directory of library
#
echo "<?php define(dbuser, \"$1\", true);" \
     "define(dbpassword, \"$pass\", true); ?>" > logins.php;

