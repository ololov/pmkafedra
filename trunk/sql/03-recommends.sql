--
--
--

DROP TABLE IF EXISTS recs CASCADE;
CREATE TABLE recs (
	id SERIAL PRIMARY KEY,
	id_book INTEGER NOT NULL REFERENCES books(id),
	ulogin VARCHAR NOT NULL REFERENCES workers(ulogin),
	record_text TEXT
);

--
--
--

DROP VIEW IF EXISTS recs_tb CASCADE;
CREATE VIEW recs_tb AS
SELECT
	id AS rec_id,
	id_book AS book_id,
	ulogin AS worker_login,
	record_text AS rec_text
FROM
	recs;

--
--
--

DROP VIEW IF EXISTS recs_w_tb CASCADE;
CREATE VIEW recs_w_tb AS
SELECT book_id, tw.* FROM recs_tb
INNER JOIN workers_tb AS tw ON(tw.worker_login = recs_tb.worker_login);

--
--
--

DROP FUNCTION IF EXISTS ADDREC(IN books.id%TYPE, IN workers.ulogin%TYPE, IN recs.record_text%TYPE);
CREATE FUNCTION ADDREC(IN books.id%TYPE, IN workers.ulogin%TYPE, IN recs.record_text%TYPE)
RETURNS INTEGER AS $$
DECLARE ret INTEGER;
BEGIN
	INSERT INTO recs(id_book, ulogin, record_text)
	VALUES ($1, $2, $3) RETURNING id INTO ret;

	RETURN ret;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такого сотрудника ПМ/книги.';
	WHEN check_violation THEN
		RAISE EXCEPTION 'Пустые комментарии запрещены.';
END;
$$ LANGUAGE plPGSQL;

--
--
--

DROP TABLE IF EXISTS bd_relation CASCADE;
CREATE TABLE bd_relation (
	id_book INTEGER REFERENCES books(id),
	dname VARCHAR NOT NULL REFERENCES disciplines(name),
	UNIQUE(id_book, dname)
);

DROP VIEW IF EXISTS bd_tb CASCADE;
CREATE VIEW bd_tb AS
SELECT id_book AS book_id, dname AS disc_name FROM bd_relation;


DROP FUNCTION IF EXISTS ADDREC(IN books.id%TYPE, IN workers.ulogin%TYPE, IN recs.record_text%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADDREC(IN books.id%TYPE, IN workers.ulogin%TYPE, IN recs.record_text%TYPE, IN VARCHAR[])
RETURNS INTEGER AS $$
DECLARE ret INTEGER;
	dset VARCHAR[];
BEGIN
	SELECT ADDREC($1, $2, $3) INTO ret;

	SELECT ARRAY(
		SELECT disc_name FROM disc_tb WHERE disc_name NOT IN(
			SELECT disc_name FROM bd_tb WHERE book_id = $1) AND disc_name = ANY($4)) INTO dset;
	IF NOT (array_length(dset, 1) = 0) THEN
		FOR i IN array_lower(dset, 1) .. array_upper(dset, 1) LOOP
			INSERT INTO bd_relation(id_book, dname)
			VALUES ($1, dset[i]);
		END LOOP;
	END IF;
	RETURN ret;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такой книги/дисциплины';
END;
$$ LANGUAGE plPGSQL;


