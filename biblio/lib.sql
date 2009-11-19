
DELIMITER //

DROP PROCEDURE IF EXISTS get_book_info //


CREATE PROCEDURE get_book_info(IN book_id INT UNSIGNED)
BEGIN
	SELECT
	tb.id AS book_id,
	tb.name AS book_name,
	tb.volume AS book_volume,
	tb.description AS book_desc,
	tb.publish AS book_publish,
	tb.year AS book_year,
	tb.isbn AS book_isbn,
	tb.posted AS book_posted,
	tb.who AS book_who,
	tb.bookpath AS book_path,
	tb.imgpath AS book_face_path,
	tb.sz AS book_size,
	tb.pages AS book_pages,
	tb.department AS book_department,
	GROUP_CONCAT(ta.full_name) AS book_authors_name,
	GROUP_CONCAT(ta.id) AS book_authors_id
	FROM bib_books AS tb, bib_authors AS ta, bib_ab_relation AS tr 
	WHERE tb.id = tr.id_book AND tr.id_author = ta.id AND tb.id = book_id;

END //

DROP PROCEDURE IF EXISTS get_book_list //

CREATE PROCEDURE get_book_list()
BEGIN
	SELECT
	tb.id AS book_id,
	tb.name AS book_name,
	tb.volume AS book_volume,
--	tb.description AS book_desc,
--	tb.publish AS book_publish,
--	tb.year AS book_year,
--	tb.isbn AS book_isbn,
--	tb.posted AS book_posted,
--	tb.who AS book_who,
	tb.bookpath AS book_path,
--	tb.imgpath AS book_face_path,
--	tb.sz AS book_size,
--	tb.pages AS book_pages,
	tb.department AS book_department,
	GROUP_CONCAT(ta.full_name) AS book_authors_name,
	GROUP_CONCAT(ta.id) AS book_authors_id
	FROM bib_books AS tb, bib_authors AS ta, bib_ab_relation AS tr 
	WHERE tb.id = tr.id_book AND tr.id_author = ta.id GROUP BY tb.id;

END //


DELIMITER ;

DROP TABLE IF EXISTS bib_books;

CREATE TABLE bib_books (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(255) NOT NULL,
	volume INT UNSIGNED,
	description TEXT,
	publish VARCHAR(255),
	year INT UNSIGNED,
	isbn VARCHAR(255),
	posted DATETIME NOT NULL,
	who VARCHAR(255) NOT NULL,
	bookpath VARCHAR(255) NOT NULL,
	imgpath VARCHAR(255),
	sz INT UNSIGNED NOT NULL,
	pages INT UNSIGNED,
	department VARCHAR(255),
	PRIMARY KEY(id)) DEFAULT CHARSET utf8;

DROP TABLE IF EXISTS bib_authors;

CREATE TABLE bib_authors (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	full_name VARCHAR(255) NOT NULL,
	PRIMARY KEY(id)) DEFAULT CHARSET utf8;

DROP TABLE IF EXISTS bib_ab_relation;

CREATE TABLE bib_ab_relation (
	id_book INT UNSIGNED NOT NULL,
	id_author INT UNSIGNED NOT NULL);

INSERT INTO bib_books (name, description, publish, year,
		    posted, bookpath, imgpath, sz, pages, who)
	VALUES ('Book1', 'Самая лучшая книга в мире',
		'Kims incorp.', 2009, NOW(), 'somewhere/in/galaxy/book1.pdf',
		'somewhere/in/galary/book1.png', 100000, 1024, 'admin'),

		('Book3', 'Самая лучшая книга в мире',
		'Kims incorp.', 2009, NOW(), 'somewhere/in/galaxy/book3.pdf',
		'somewhere/in/galary/book3.png', 200000, 2048, 'admin'),

	       ('Book2', 'Самая худшая книга в мире',
		'Trash incorp.', 2009, NOW(), 'somewhere/in/trash/book2.pdf',
		'somewhere/in/galary/trash/book2.png', 1000, 102, 'admin'),

	       ('Book4',
	        'Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.',
		'Best Trash incorp.', 2003, NOW(),
		'somewhere/in/trash/book4.pdf',
		'somewhere/in/galary/trash/book4.png',
		1000000, 10200, 'admin');

INSERT INTO bib_authors(full_name) VALUES
				('Maxim Vladimirovi4 Kim'),
				('M. V. Kim'),
				('Maxim V. Kim');

INSERT INTO bib_ab_relation VALUES (1, 1), (3, 3), (2, 1), (2, 2);
