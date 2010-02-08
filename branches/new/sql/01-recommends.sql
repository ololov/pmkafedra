--
--
--

DROP TABLE IF EXISTS recs CASCADE;
CREATE TABLE recs (
	id_book INTEGER NOT NULL REFERENCES books(id),
	id_worker INTEGER NOT NULL REFERENCES workers(id),
	record_text TEXT,
	CHECK(record_text != '')
);

--
--
--

DROP VIEW IF EXISTS recs_tb CASCADE;
CREATE VIEW recs_tb AS
SELECT
	id_book AS book_id,
	id_worker AS worker_id,
	record_text AS rec_text
FROM
	recs;

DROP VIEW IF EXISTS recs_w_tb CASCADE;
CREATE VIEW recs_w_tb AS
SELECT book_id, tw.* FROM recs_tb
INNER JOIN workers_tb AS tw ON(tw.worker_id = recs_tb.worker_id);

--
--
--

DROP FUNCTION IF EXISTS ADDREC(IN books.id%TYPE, IN workers.id%TYPE, IN recs.record_text%TYPE);
CREATE FUNCTION ADDREC(IN books.id%TYPE, IN workers.id%TYPE, IN recs.record_text%TYPE)
RETURNS VOID AS $$
BEGIN
	INSERT INTO recs(id_book, id_worker, record_text)
	VALUES ($1, $2, $3);
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такого сотрудника ПМ/книги.';
	WHEN check_violation THEN
		RAISE EXCEPTION 'Пустые комментарии запрещены.';
END;
$$ LANGUAGE plPGSQL;


DROP FUNCTION IF EXISTS ADDREC(IN books.id%TYPE, IN workers.user_login%TYPE, IN recs.record_text%TYPE);
CREATE FUNCTION ADDREC(IN books.id%TYPE, IN workers.user_login%TYPE, IN recs.record_text%TYPE)
RETURNS VOID AS $$
DECLARE	wid INTEGER;
BEGIN
	SELECT id INTO wid FROM workers WHERE user_login = $2;
	PERFORM ADDREC($1, wid);
END;
$$ LANGUAGE plPGSQL;

