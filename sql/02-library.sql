-- DROP LANGUAGE IF EXISTS plpgsql CASCADE;
-- CREATE LANGUAGE plpgsql;

SET check_function_bodies=true;
SET client_encoding='UTF8';

--
-- * * * * * * * * * T A B L E S * * * * * * * * * * *
--
--
DROP TABLE IF EXISTS books CASCADE;
CREATE TABLE books (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	posted TIMESTAMP NOT NULL,
	who VARCHAR NOT NULL REFERENCES workers(ulogin),
	bookpath VARCHAR(255) NOT NULL UNIQUE,
	ispublic BOOLEAN NOT NULL DEFAULT FALSE,
	sz INT NOT NULL,
	volume INT,
	description TEXT,
	publish VARCHAR(255),
	year INT,
	isbn VARCHAR(255),
	imgpath VARCHAR(255),
	pages INT,
	CHECK(name != ''),
	CHECK(who != ''),
	CHECK(bookpath != ''),
	CHECK(sz > 0),
	CHECK(volume > 0 OR volume IS NULL),
	CHECK(description != '' OR description IS NULL),
	CHECK(publish != '' OR publish IS NULL),
	CHECK(isbn != '' OR isbn IS NULL),
	CHECK(imgpath != '' OR imgpath IS NULL),
	CHECK((year > 1000 AND year <= EXTRACT(year FROM NOW())) OR year IS NULL),
	CHECK(pages > 0 OR pages IS NULL)
);

DROP TABLE IF EXISTS authors CASCADE;
DROP TABLE IF EXISTS ab_relation CASCADE;
DROP TABLE IF EXISTS departments CASCADE;
DROP TABLE IF EXISTS db_relation CASCADE;

DROP TABLE IF EXISTS book_authors CASCADE;
CREATE TABLE book_authors (
	id_book INTEGER NOT NULL REFERENCES books(id),
	author_name VARCHAR NOT NULL,
	UNIQUE(id_book, author_name)
);

DROP TABLE IF EXISTS book_deps CASCADE;
CREATE TABLE book_deps (
	id_book INTEGER NOT NULL REFERENCES books(id),
	dep_name VARCHAR NOT NULL,
	UNIQUE(id_book, dep_name)
);

--
--
-- * * * * * * * * * * * * V I E W S * * * * * * * * * * * * *
--
--

DROP VIEW IF EXISTS books_tb CASCADE;
CREATE VIEW books_tb AS
SELECT
	id AS book_id,
	name AS book_name,
	volume AS book_volume,
	description AS book_desc,
	ispublic AS book_ispublic,
	publish AS book_publish,
	year AS book_year,
	isbn AS book_isbn,
	posted AS book_posted,
	who AS worker_login,
	(SELECT worker_name FROM workers_tb WHERE worker_login = who) AS book_who,
	bookpath AS book_path,
	imgpath AS book_face,
	sz AS book_size,
	pages AS book_pages
FROM
	books;

--
-- Removing old tables
--
DROP VIEW IF EXISTS authors_tb CASCADE;
DROP VIEW IF EXISTS deps_tb CASCADE;

DROP VIEW IF EXISTS  ab_tb CASCADE;
CREATE VIEW ab_tb AS
SELECT
	id_book AS book_id,
	author_name AS author_name
FROM
	book_authors;

DROP VIEW IF EXISTS db_tb CASCADE;
CREATE VIEW db_tb AS
SELECT
	id_book AS book_id,
	dep_name AS dep_name
FROM
	book_deps;

--
--
-- To simplify sql requests
--
--
DROP VIEW IF EXISTS abfull_tb CASCADE;
CREATE VIEW abfull_tb AS
SELECT
	tb.*,
	author_name
FROM ab_tb AS ab
INNER JOIN books_tb AS tb ON(tb.book_id = ab.book_id);

DROP VIEW IF EXISTS dbfull_tb CASCADE;
CREATE VIEW dbfull_tb AS
SELECT
	tb.*,
	dep_name
FROM db_tb AS db
INNER JOIN books_tb AS tb ON(tb.book_id = db.book_id);

--
--
-- * * * * * * * * * * F U N C T I O N S * * * * * * * * * * * *
--
--

--
-- Removing old functions
--
DROP FUNCTION IF EXISTS ADDDEPARTMENTS(IN VARCHAR[]);
DROP FUNCTION IF EXISTS ADDAUTHORS(IN VARCHAR[]);
DROP FUNCTION IF EXISTS ADDAB_REL(IN INTEGER[], IN INTEGER[]);

DROP FUNCTION IF EXISTS ADDAUTHORS(IN books.id%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADDAUTHORS(IN books.id%TYPE, IN VARCHAR[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NULL THEN
		RETURN;
	END IF; 
	IF NOT (ARRAY_LENGTH($2, 1) = 0) THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
		BEGIN
			INSERT INTO book_authors(id_book,author_name)
			VALUES($1, $2[i]);
		EXCEPTION
			WHEN unique_violation THEN
		END;
		END LOOP;
	END IF;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такой книги.';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADDDEPARTMENTS(IN books.id%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADDDEPARTMENTS(IN books.id%TYPE, IN VARCHAR[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NULL THEN
		RETURN;
	END IF;

	IF NOT (ARRAY_LENGTH($2, 1) = 0) THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
		BEGIN
			INSERT INTO book_deps(id_book,dep_name)
			VALUES($1, $2[i]);
		EXCEPTION
			WHEN unique_violation THEN
		END;
		END LOOP;
	END IF;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такой книги.';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADDBOOK(IN books.name%TYPE,
				IN VARCHAR[],
				IN books.who%TYPE,
				IN books.sz%TYPE,
				IN books.bookpath%TYPE,
				IN books.volume%TYPE,
				IN books.description%TYPE,
				IN books.publish%TYPE,
				IN books.year%TYPE,
				IN books.isbn%TYPE,
				IN VARCHAR[]);
CREATE FUNCTION ADDBOOK(IN books.name%TYPE,
			IN VARCHAR[],
			IN books.who%TYPE,
			IN books.sz%TYPE,
			IN books.bookpath%TYPE,
			IN books.volume%TYPE,
			IN books.description%TYPE,
			IN books.publish%TYPE,
			IN books.year%TYPE,
			IN books.isbn%TYPE,
			IN VARCHAR[])
RETURNS INTEGER AS $$
DECLARE bname   books.name%TYPE;
	bwho    books.who%TYPE;
	bsz	books.sz%TYPE;
	bpath   books.bookpath%TYPE;
	bvol    books.volume%TYPE;
	bdescr  books.description%TYPE;
	bpub    books.publish%TYPE;
	byear   books.year%TYPE;
	bisbn   books.isbn%TYPE;

	aids    INTEGER[];
	bid     books.id%TYPE;
BEGIN

	bname := TRIM(BOTH ' ' FROM $1);
	IF bname IS NULL OR bname = '' THEN
		RAISE EXCEPTION 'Название книги не может быть пустым';
	END IF;

	bwho := $3;

	bsz := $4;
	IF bsz IS NULL OR bsz <= 0 THEN
		RAISE EXCEPTION 'Размер книги должен быть больше нуля';
	END IF;


	bpath := TRIM(BOTH ' ' FROM $5);
	IF bpath IS NULL OR bpath = '' THEN
		RAISE EXCEPTION 'Путь к книги не может быть пустым';
	END IF;

	bvol := $6;
	IF bvol IS NOT NULL AND bvol <= 0 THEN
		RAISE EXCEPTION 'Том не может быть меньше единицы';
	END IF;

	bdescr := TRIM(BOTH ' ' FROM $7);
	IF bdescr IS NOT NULL AND bdescr = '' THEN
		RAISE EXCEPTION 'Описание книги не может быть пустым';
	END IF;

	bpub := TRIM(BOTH ' ' FROM $8);
	IF bpub IS NOT NULL AND bpub = '' THEN
		RAISE EXCEPTION 'Название издательства не может быть пустым';
	END IF;

	byear := $9;
	IF byear IS NOT NULL AND (byear <= 0 OR byear > EXTRACT(year FROM NOW())) THEN
		RAISE EXCEPTION 'Недопустимый год выпуска';
	END IF;

	bisbn := TRIM(BOTH ' ' FROM $10);
	IF bisbn IS NOT NULL AND bisbn = '' THEN
		RAISE EXCEPTION 'ISBN не может быть пустым';
	END IF;

	INSERT INTO books(name,  who,  sz,  bookpath, volume, description, publish, year,  isbn,  posted)
	VALUES		 (bname, bwho, bsz, bpath,    bvol,   bdescr,      bpub,    byear, bisbn, NOW())
	RETURNING id INTO bid;

	PERFORM ADDAUTHORS(bid, $2);
	PERFORM ADDDEPARTMENTS(bid, $11);

	IF bdescr IS NULL THEN
		bdescr := bwho || ' Поленился написать описание книги.';
	END IF;

	PERFORM ADD_NEWS(bwho, 'Новости библиотеки',
			'Добавлена новая книга: "' || bname || '"', bdescr);

	RETURN bid;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Неизвестный сотрудник кафедры';
END;
$$ LANGUAGE plPGSQL;

--
--
--
--
--

DROP FUNCTION IF EXISTS MODIFYBOOK(IN books.id%TYPE, IN books.imgpath%TYPE, IN books.pages%TYPE);
CREATE FUNCTION MODIFYBOOK(IN books.id%TYPE, IN books.imgpath%TYPE, IN books.pages%TYPE)
RETURNS VOID AS $$
BEGIN
	UPDATE books SET imgpath = $2, pages = $3 WHERE id = $1;
END;
$$ LANGUAGE plPGSQL;

