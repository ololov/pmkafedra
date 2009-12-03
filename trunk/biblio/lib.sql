
SET check_function_bodies=true;
SET client_encoding='UTF8';
--
--
-- * * * * * * * * * T A B L E S * * * * * * * * * * *
--
--
DROP TABLE IF EXISTS books CASCADE;

CREATE TABLE books (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	volume INT ,
	description TEXT,
	publish VARCHAR(255),
	year INT ,
	isbn VARCHAR(255),
	posted TIMESTAMP NOT NULL,
	who VARCHAR(255) NOT NULL,
	bookpath VARCHAR(255) NOT NULL,
	imgpath VARCHAR(255),
	sz INT  NOT NULL,
	pages INT ,
	department VARCHAR(255),
	CHECK(sz > 0),
	CHECK(year > 1000 OR year IS NULL),
	CHECK(pages > 0 OR pages IS NULL)
);

DROP TABLE IF EXISTS authors CASCADE;

CREATE TABLE authors (
	id SERIAL NOT NULL PRIMARY KEY,
	full_name VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS ab_relation;

CREATE TABLE ab_relation (
	id_book INT NOT NULL REFERENCES books(id),
	id_author INT NOT NULL REFERENCES authors(id)
);

--
--
-- * * * * * * * * * * * * V I E W S * * * * * * * * * * * * *
--
--

DROP VIEW IF EXISTS books_tb;
CREATE VIEW books_tb AS
SELECT
	id AS book_id,
	name AS book_name,
	volume AS book_volume,
	description AS book_desc,
	publish AS book_publish,
	year AS book_year,
	isbn AS book_isbn,
	posted AS book_posted,
	who AS book_who,
	bookpath AS book_path,
	imgpath AS book_face,
	sz AS book_size,
	pages AS book_pages,
	department AS book_dep
FROM
	books;

DROP VIEW IF EXISTS authors_tb;
CREATE VIEW authors_tb AS
SELECT
	id AS author_id,
	full_name AS author_name
FROM
	authors;

DROP VIEW IF EXISTS  ab_tb;
CREATE VIEW ab_tb AS
SELECT
	id_book AS book_id,
	(SELECT name FROM books WHERE id = id_book) AS book_name,
	id_author AS author_id,
	(SELECT full_name FROM authors WHERE id = id_author) AS author_name
FROM
	ab_relation;

--
--
-- * * * * * * * * * * F U N C T I O N S * * * * * * * * * * * *
--
--
DROP FUNCTION IF EXISTS CHECKNAME(IN authors.full_name%TYPE);
CREATE FUNCTION CHECKNAME(IN authors.full_name%TYPE)
RETURNS void AS $$
BEGIN
	IF $1 !~* '^[a-zА-Яа-я]{2,16}( ([А-Яа-яa-z][.]|[a-zА-Яа-я]{2,16})){2}' THEN
		RAISE EXCEPTION 'Неправильное имя автора: (%)', $1;
	END IF;
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADDAUTHORS(IN VARCHAR[]);
CREATE FUNCTION ADDAUTHORS(IN VARCHAR[])
RETURNS void AS $$
DECLARE tmp VARCHAR;
BEGIN
	IF array_lower($1, 1) IS NULL THEN
		RETURN;
	END IF;
	FOR i IN array_lower($1, 1) .. array_upper($1, 1) LOOP
		PERFORM CHECKNAME(($1)[i]);
		SELECT full_name INTO tmp FROM authors WHERE full_name = ($1)[i];
		IF tmp IS NULL THEN
			INSERT INTO authors(full_name) VALUES(($1)[i]);
		END IF;
	END LOOP;
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADDBOOK(IN books.name%TYPE,
				IN VARCHAR[],
				IN books.volume%TYPE,
				IN books.description%TYPE,
				IN books.publish%TYPE,
				IN books.year%TYPE,
				IN books.isbn%TYPE,
				IN books.who%TYPE,
				IN books.bookpath%TYPE,
				IN books.department%TYPE,
				IN books.sz%TYPE);

CREATE FUNCTION ADDBOOK(IN books.name%TYPE,
			IN VARCHAR[],
			IN books.volume%TYPE,
			IN books.description%TYPE,
			IN books.publish%TYPE,
			IN books.year%TYPE,
			IN books.isbn%TYPE,
			IN books.who%TYPE,
			IN books.bookpath%TYPE,
			IN books.department%TYPE,
			IN books.sz%TYPE)
RETURNS void AS $$
DECLARE bname	books.name%TYPE;
	bvol	books.volume%TYPE;
	bdescr	books.description%TYPE;
	byear	books.year%TYPE;
	bpub	books.publish%TYPE;
	bisbn	books.isbn%TYPE;
	bwho	books.who%TYPE;
	bpath	books.bookpath%TYPE;
	bdep	books.department%TYPE;

	aids	INTEGER[];
	bid	books.id%TYPE;
BEGIN
	PERFORM ADDAUTHORS($2);

	bname := TRIM(BOTH ' ' FROM $1);
        IF bname = '' THEN
		RAISE EXCEPTION 'Название книги не может быть пустым';
	END IF;
	bvol := $3;
	IF bvol = 0 THEN
		bvol := NULL;
	END IF;
	bdescr := TRIM(BOTH ' ' FROM $4);
	IF bdescr = '' THEN
		bdescr := NULL;
	END IF;
	bpub := TRIM(BOTH ' ' FROM $5);
	IF bpub = '' THEN
		bpub := NULL;
	END IF;
	byear := $6;
	IF byear > EXTRACT(year FROM NOW()) THEN
		RAISE EXCEPTION 'Год выпуска не может превышать текущий';
	END IF;
	IF byear = 0 THEN
		byear := NULL;
	END IF;
	bisbn := TRIM(BOTH ' ' FROM $7);
	IF bisbn = '' THEN
		bisbn := NULL;
	END IF;
	bwho := TRIM(BOTH ' ' FROM $8);
	IF bwho = '' THEN
		RAISE EXCEPTION 'Поле who не может быть пустым';
	END IF;
	bpath := TRIM(BOTH ' ' FROM $9);
	IF bpath = '' THEN
		RAISE EXCEPTION 'Путь не может быть пустым';
	END IF;
	bdep := TRIM(BOTH ' ' FROM $10);
	if bdep = '' THEN
		bdep := NULL;
	END IF;
	INSERT INTO books(name, volume, description, publish, year, isbn, who, bookpath, department, posted, sz)
	VALUES (bname, bvol, bdescr, bpub, byear, bisbn, bwho, bpath, bdep, NOW(), $11)
	RETURNING id INTO bid;

	SELECT ARRAY(SELECT id FROM authors WHERE full_name = ANY($2)) INTO aids;
	FOR i IN array_lower(aids, 1) .. array_upper(aids, 1) LOOP
		INSERT INTO ab_relation(id_book, id_author)
		VALUES (bid, aids[i]);
	END LOOP;
END;
$$ LANGUAGE plPGSQL;

--
--
--
-- * * * * * * * * S O M E  D A T A * * * * * * * * * * * * 
--
--


--      ADDBOOK(IN books.name%TYPE,
--		IN VARCHAR[],
--		IN books.volume%TYPE,
--		IN books.description%TYPE,
--		IN books.publish%TYPE,
--		IN books.year%TYPE,
--		IN books.isbn%TYPE,
--		IN books.who%TYPE,
--		IN books.bookpath%TYPE,
--		IN books.department%TYPE,
--		IN books.sz%TYPE);

SELECT  ADDBOOK(VARCHAR 'Book1',
		ARRAY['Kim M. V.'],
		1,
		CAST('Самая лучшая книга в мире' AS TEXT),
		CAST('Kims incorp.' AS VARCHAR),
	       	2009,
	       	CAST('isbn-123-213123-231-3123' AS VARCHAR),
		CAST('admin' AS VARCHAR),
		CAST('somewhere/in/galaxy/book1.pdf' AS VARCHAR),
		CAST('Наука' AS VARCHAR),
		100000);

SELECT  ADDBOOK('Book3',
		ARRAY['Somebody Someone Something', 'Anyone anybody Anything'],
		2,
       		'Книга так себе',
		'Trash incorp.',
	       	2009,
		'isbn-123-213-123-1-23',
		'somebody',
		'somewhere/in/galaxy/book3.pdf',
		'}{aker',
		200000);

SELECT  ADDBOOK('Book3',
		ARRAY['Bad Worse Worst', 'Ugly Ugly Ugly'],
		2,
	        'Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.Вы читаете самое длинное описание книги, когда либо было написано человеком. Оно настолько длинное, что у Вас не должно хватить времени его прочесть! Оно занимает кучу места на жестком диске и займет кучу времени если Вы не закончите читать прямо сейчас. Зачем кто-то написал такое длинное описание никто не знает, в том числе и сам автор. Хотя нет, знает! Если серьезно оно нужно, чтобы проверить корректную работу функции выводящее описании книги. При очень длинном описании она должна обрезать описании и в конце последнее слова ставить ..., хотя бы ... для начала.',
		'O God! incorp.',
	       	2009,
		'isbn-123-213-123-1-23',
		'somebody',
		'somewhere/in/galaxy/book3.pdf',
		'Пустозвонство',
		200000);

