--
--
--

DROP TABLE IF EXISTS wd_relation CASCADE;
CREATE TABLE wd_relation (
	ulogin VARCHAR NOT NULL REFERENCES workers(ulogin),
	dname VARCHAR NOT NULL REFERENCES disciplines(name)
);

DROP VIEW IF EXISTS wd_tb CASCADE;
CREATE VIEW wd_tb AS
SELECT
	ulogin AS worker_login,
	dname AS disc_name
FROM
	wd_relation;

DROP VIEW IF EXISTS wdfull_tb CASCADE;
CREATE VIEW wdfull_tb AS
SELECT td.*, tw.* FROM wd_tb AS tb
INNER JOIN workers_tb AS tw ON(tb.worker_login = tw.worker_login)
INNER JOIN disc_tb AS td ON(td.disc_name = tb.disc_name);

--
--
--

DROP FUNCTION IF EXISTS ADD_WORKER2DISC(IN workers.ulogin%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADD_WORKER2DISC(IN workers.ulogin%TYPE, IN VARCHAR[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO wd_relation(ulogin, dname)
			VALUES($1, $2[i]);
		END LOOP;
	END IF;
EXCEPTION
	WHEN unique_violation THEN
		RAISE EXCEPTION 'Такая запись уже есть';
END;
$$ LANGUAGE plPGSQL;


DROP FUNCTION IF EXISTS ADD_DISC2WORKER(IN disciplines.name%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADD_DISC2WORKER(IN disciplines.name%TYPE, IN VARCHAR[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO wd_relation(dname, ulogin)
			VALUES($1, $2[i]);
		END LOOP;
	END IF;
EXCEPTION
	WHEN unique_violation THEN
		RAISE EXCEPTION 'Такая запись уже есть';
END;
$$ LANGUAGE plPGSQL;

--
--
--

DROP FUNCTION IF EXISTS ADDDISC(IN disciplines.name%TYPE,
				IN INTEGER[],
				IN disciplines.lessons%TYPE,
				IN disciplines.practices%TYPE,
				IN disciplines.labs%TYPE,
				IN disciplines.courseovik%TYPE,
				IN disciplines.control%TYPE,
				IN VARCHAR[]);
CREATE FUNCTION ADDDISC(IN disciplines.name%TYPE,
			IN INTEGER[],
			IN disciplines.lessons%TYPE,
			IN disciplines.practices%TYPE,
			IN disciplines.labs%TYPE,
			IN disciplines.courseovik%TYPE,
			IN disciplines.control%TYPE,
			IN VARCHAR[])
RETURNS VOID AS $$
BEGIN
	INSERT INTO disciplines(name, lessons, practices, labs, courseovik, control)
	VALUES($1, $3, $4, $5, $6, $7);

	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO dk_relation(dname, course)
			VALUES($1, $2[i]);
		END LOOP;
		PERFORM ADD_DISC2WORKER($1, $8);
	END IF;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Некорректные данные';
END;
$$ LANGUAGE plPGSQL;

--
--
--
--

SELECT ADDDISC('Алгебра и аналитическая геометрия', ARRAY[1,2], 64, 35, 0, 'нет', 'экзамен', ARRAY['prepod']);
SELECT ADDDISC('Математический анализ', ARRAY[1,2], 88, 64, 0, '"Поверхности"', 'экзамен', ARRAY['prepod']);
SELECT ADDDISC('Программные и аппаратные средства информатики', ARRAY[1], 10, 3, 6, 'нет', 'экзамен', ARRAY['prepod']);
SELECT ADDDISC('Физика', ARRAY[1,2], 54, 32, 12, 'нет', 'экзамен', ARRAY['prepod']);

