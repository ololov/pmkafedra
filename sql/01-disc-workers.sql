--
--
--

DROP TABLE IF EXISTS wd_relation CASCADE;
CREATE TABLE wd_relation (
	id_worker INTEGER NOT NULL REFERENCES workers(id),
	id_disc INTEGER NOT NULL REFERENCES disciplines(id)
);

DROP VIEW IF EXISTS wd_tb CASCADE;
CREATE VIEW wd_tb AS
SELECT
	id_worker AS worker_id,
	id_disc AS disc_id
FROM
	wd_relation;

DROP VIEW IF EXISTS wdfull_tb CASCADE;
CREATE VIEW wdfull_tb AS
SELECT td.*, tw.* FROM wd_tb AS tb
INNER JOIN workers_tb AS tw ON(tb.worker_id = tw.worker_id)
INNER JOIN disc_tb AS td ON(td.disc_id = tb.disc_id);

--
--
--

DROP FUNCTION IF EXISTS ADD_WORKER2DISC(IN workers.id%TYPE, IN INTEGER[]);
CREATE FUNCTION ADD_WORKER2DISC(IN workers.id%TYPE, IN INTEGER[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO wd_relation(id_worker, id_disc)
			VALUES($1, $2[i]);
		END LOOP;
	END IF;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такого преподавателя/дисциплины';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADD_WORKER2DISC(IN workers.user_login%TYPE, IN INTEGER[]);
CREATE FUNCTION ADD_WORKER2DISC(IN workers.user_login%TYPE, IN INTEGER[])
RETURNS VOID AS $$
DECLARE wid INTEGER;
BEGIN
	SELECT id INTO wid FROM workers WHERE user_login = $1;
	PERFORM ADD_WORKER2DISC(wid, $2);
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADD_DISC2WORKER(IN disciplines.id%TYPE, IN INTEGER[]);
CREATE FUNCTION ADD_DISC2WORKER(IN disciplines.id%TYPE, IN INTEGER[])
RETURNS VOID AS $$
BEGIN
	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO wd_relation(id_disc, id_worker)
			VALUES($1, $2[i]);
		END LOOP;
	END IF;
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Нет такого преподавателя/дисциплины';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADD_DISC2WORKER(IN disciplines.id%TYPE, IN VARCHAR[]);
CREATE FUNCTION ADD_DISC2WORKER(IN disciplines.id%TYPE, IN VARCHAR[])
RETURNS VOID AS $$
DECLARE wid INTEGER[];
BEGIN
	SELECT ARRAY(SELECT id FROM workers WHERE user_login = ANY($2)) INTO wid;
	PERFORM ADD_DISC2WORKER($1, wid);
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
				IN INTEGER[]);
CREATE FUNCTION ADDDISC(IN disciplines.name%TYPE,
			IN INTEGER[],
			IN disciplines.lessons%TYPE,
			IN disciplines.practices%TYPE,
			IN disciplines.labs%TYPE,
			IN disciplines.courseovik%TYPE,
			IN disciplines.control%TYPE,
			IN INTEGER[])
RETURNS INTEGER AS $$
DECLARE did INTEGER;
BEGIN
	INSERT INTO disciplines(name, lessons, practices, labs, courseovik, control)
	VALUES($1, $3, $4, $5, $6, $7) RETURNING id INTO did;

	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO dk_relation(id_disc, course)
			VALUES(did, $2[i]);
		END LOOP;
		PERFORM ADD_DISC2WORKER(did, $8);
	END IF;
	RETURN did;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Некорректные данные';
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
RETURNS INTEGER AS $$
DECLARE did INTEGER;
BEGIN
	INSERT INTO disciplines(name, lessons, practices, labs, courseovik, control)
	VALUES($1, $3, $4, $5, $6, $7) RETURNING id INTO did;

	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO dk_relation(id_disc, course)
			VALUES(did, $2[i]);
		END LOOP;
		PERFORM ADD_DISC2WORKER(did, $8);
	END IF;
	RETURN did;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Некорректные данные';
END;
$$ LANGUAGE plPGSQL;

--
--
--
--

SELECT ADDDISC('Алгебра и аналитическая геометрия', ARRAY[1,2], 64, 35, 0, 'нет', 'экзамен', ARRAY[1]);
SELECT ADDDISC('Математический анализ', ARRAY[1,2], 88, 64, 0, '"Поверхности"', 'экзамен', ARRAY['prepod']);
SELECT ADDDISC('Программные и аппаратные средства информатики', ARRAY[1], 10, 3, 6, 'нет', 'экзамен', ARRAY[1]);
SELECT ADDDISC('Физика', ARRAY[1,2], 54, 32, 12, 'нет', 'экзамен', ARRAY['prepod']);

