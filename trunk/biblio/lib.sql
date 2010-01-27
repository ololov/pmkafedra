DROP LANGUAGE IF EXISTS plpgsql CASCADE;
CREATE LANGUAGE plpgsql;

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
	posted TIMESTAMP NOT NULL,
	who VARCHAR(255) NOT NULL,
	bookpath VARCHAR(255) NOT NULL,
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
CREATE TABLE authors (
	id SERIAL NOT NULL PRIMARY KEY,
	full_name VARCHAR(255) NOT NULL,
	CHECK(full_name != '')
);

DROP TABLE IF EXISTS ab_relation;
CREATE TABLE ab_relation (
	id_book INT NOT NULL REFERENCES books(id),
	id_author INT NOT NULL REFERENCES authors(id)
);

DROP TABLE IF EXISTS departments CASCADE;
CREATE TABLE departments (
	id SERIAL NOT NULL PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	CHECK(name != '')
);

DROP TABLE IF EXISTS db_relation;
CREATE TABLE db_relation (
	id_book INT NOT NULL REFERENCES books(id),
	id_dep INT NOT NULL REFERENCES departments(id)
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
	pages AS book_pages
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
	id_author AS author_id
FROM
	ab_relation;

DROP VIEW IF EXISTS deps_tb;
CREATE VIEW deps_tb AS
SELECT
	id AS dep_id,
	name AS dep_name
FROM
	departments;

DROP VIEW IF EXISTS db_tb;
CREATE VIEW db_tb AS
SELECT
	id_book AS book_id,
	id_dep AS dep_id
FROM
	db_relation;

--
--
-- * * * * * * * * * * F U N C T I O N S * * * * * * * * * * * *
--
--
DROP FUNCTION IF EXISTS ADDAUTHORS(IN VARCHAR[]);
CREATE FUNCTION ADDAUTHORS(IN VARCHAR[])
RETURNS void AS $$
DECLARE tmp VARCHAR;
	name VARCHAR;
BEGIN
	-- Если массив не имеет элементов
	IF array_lower($1, 1) IS NULL THEN
		RETURN;
	END IF;
	FOR i IN array_lower($1, 1) .. array_upper($1, 1) LOOP

		IF ($1)[i] IS NOT NULL THEN
		       	name := TRIM(BOTH ' ' FROM ($1)[i]);
		ELSE
			RAISE EXCEPTION 'В поле "автор" есть нулевое значение';
		END IF;

		SELECT full_name INTO tmp FROM authors WHERE full_name = name;
		IF tmp IS NULL THEN
			INSERT INTO authors(full_name) VALUES(name);
		END IF;
	END LOOP;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Имя автора не может быть пустым';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADDDEPARTMENTS(IN VARCHAR[]);
CREATE FUNCTION ADDDEPARTMENTS(IN VARCHAR[])
RETURNS VOID AS $$
DECLARE dname VARCHAR;
	tmp  VARCHAR;
BEGIN
	IF array_lower($1, 1) IS NULL THEN
		RETURN;
	END IF;
	FOR i IN array_lower($1, 1) .. array_upper($1, 1) LOOP
		if ($1)[i] IS NOT NULL THEN
			dname := TRIM(BOTH ' ' FROM ($1)[i]);
		ELSE
			RAISE EXCEPTION 'Попытка добавить нулевое значение';
		END IF;

		SELECT tb.name INTO tmp FROM departments AS tb WHERE tb.name = dname;
		IF tmp IS NULL THEN
			INSERT INTO departments(name) VALUES(dname);
		END IF;
	END LOOP;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Название не может быть нулевым';
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
				IN books.isbn%TYPE);
CREATE FUNCTION ADDBOOK(IN books.name%TYPE,
			IN VARCHAR[],
			IN books.who%TYPE,
			IN books.sz%TYPE,
			IN books.bookpath%TYPE,
			IN books.volume%TYPE,
			IN books.description%TYPE,
			IN books.publish%TYPE,
			IN books.year%TYPE,
			IN books.isbn%TYPE)
RETURNS void AS $$
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
	PERFORM ADDAUTHORS($2);

	bname := TRIM(BOTH ' ' FROM $1);
	IF bname IS NULL OR bname = '' THEN
		RAISE EXCEPTION 'Название книги не может быть пустым';
	END IF;

	bwho := TRIM(BOTH ' ' FROM $3);
	IF bwho IS NULL OR bwho = '' THEN
		RAISE EXCEPTION 'Имя пользователя не может быть пустым';
	END IF;

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
--
--

