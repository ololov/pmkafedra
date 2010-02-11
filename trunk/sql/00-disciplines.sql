
--
--
--

DROP TABLE IF EXISTS disciplines CASCADE;
CREATE TABLE disciplines (
	id SERIAL PRIMARY KEY,
	name VARCHAR NOT NULL,
	lessons INTEGER NOT NULL,
	practices INTEGER NOT NULL,
	labs INTEGER NOT NULL,
	courseovik VARCHAR,
	control VARCHAR,
	UNIQUE(name),
	CHECK(lessons > -1),
	CHECK(practices > -1),
	CHECK(labs > -1),
	CHECK(courseovik != '')
);

DROP VIEW IF EXISTS disc_tb CASCADE;
CREATE VIEW disc_tb AS
SELECT
	id AS disc_id,
	name AS disc_name,
	lessons AS disc_lessons,
	practices AS disc_practices,
	labs AS disc_labs,
	courseovik AS disc_courseovik
FROM
	disciplines;

DROP TABLE IF EXISTS dk_relation CASCADE;
CREATE TABLE dk_relation (
	id_disc INTEGER NOT NULL REFERENCES disciplines(id),
	course INTEGER NOT NULL,
	UNIQUE(id_disc, course),
	CHECK(course > 0 AND course < 6)
);

DROP VIEW IF EXISTS dk_tb CASCADE;
CREATE VIEW dk_tb AS
SELECT
	id_disc AS disc_id,
	course AS course_number
FROM
	dk_relation;

--
--
--

DROP FUNCTION IF EXISTS ADDDISC(IN disciplines.name%TYPE,
				IN INTEGER[],
				IN disciplines.lessons%TYPE,
				IN disciplines.practices%TYPE,
				IN disciplines.labs%TYPE,
				IN disciplines.courseovik%TYPE,
				IN disciplines.control%TYPE);
CREATE FUNCTION ADDDISC(IN disciplines.name%TYPE,
			IN INTEGER[],
			IN disciplines.lessons%TYPE,
			IN disciplines.practices%TYPE,
			IN disciplines.labs%TYPE,
			IN disciplines.courseovik%TYPE,
			IN disciplines.control%TYPE)
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

