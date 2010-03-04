
--
--
--

DROP TABLE IF EXISTS disciplines CASCADE;
CREATE TABLE disciplines (
	name VARCHAR PRIMARY KEY,
	lessons INTEGER NOT NULL,
	practices INTEGER NOT NULL,
	labs INTEGER NOT NULL,
	courseovik VARCHAR,
	control VARCHAR,
	about VARCHAR,
	prof BOOLEAN NOT NULL DEFAULT FALSE,
	UNIQUE(name),
	CHECK(lessons > -1),
	CHECK(practices > -1),
	CHECK(labs > -1),
	CHECK(courseovik != '')
);

DROP VIEW IF EXISTS disc_tb CASCADE;
CREATE VIEW disc_tb AS
SELECT
	name AS disc_name,
	lessons AS disc_lessons,
	practices AS disc_practices,
	labs AS disc_labs,
	courseovik AS disc_courseovik,
	about AS disc_about,
	prof AS isprof
FROM
	disciplines;
--
DROP VIEW IF EXISTS prof_disc_tb CASCADE;
CREATE VIEW prof_disc_tb AS
SELECT * FROM disc_tb WHERE isprof;

DROP TABLE IF EXISTS dk_relation CASCADE;
CREATE TABLE dk_relation (
	dname VARCHAR NOT NULL REFERENCES disciplines(name),
	course INTEGER NOT NULL,
	UNIQUE(dname, course),
	CHECK(course > 0 AND course < 6)
);

DROP VIEW IF EXISTS dk_tb CASCADE;
CREATE VIEW dk_tb AS
SELECT
	dname AS disc_name,
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
				IN disciplines.control%TYPE,
				IN disciplines.about%TYPE);
CREATE FUNCTION ADDDISC(IN disciplines.name%TYPE,
			IN INTEGER[],
			IN disciplines.lessons%TYPE,
			IN disciplines.practices%TYPE,
			IN disciplines.labs%TYPE,
			IN disciplines.courseovik%TYPE,
			IN disciplines.control%TYPE,
			IN disciplines.about%TYPE)
RETURNS VOID AS $$
BEGIN
	INSERT INTO disciplines(name, lessons, practices, labs, courseovik, control, about)
	VALUES($1, $3, $4, $5, $6, $7, $8);

	IF $2 IS NOT NULL THEN
		FOR i IN array_lower($2, 1) .. array_upper($2, 1) LOOP
			INSERT INTO dk_relation(dname, course)
			VALUES($1, $2[i]);
		END LOOP;
	END IF;
EXCEPTION
	WHEN check_violation THEN
		RAISE EXCEPTION 'Ошибка: Некорректные данные';
END;
$$ LANGUAGE plPGSQL;

DROP FUNCTION IF EXISTS ADD_PROFDISC(IN disciplines.name%TYPE,
				IN INTEGER[],
				IN disciplines.lessons%TYPE,
				IN disciplines.practices%TYPE,
				IN disciplines.labs%TYPE,
				IN disciplines.courseovik%TYPE,
				IN disciplines.control%TYPE,
				IN disciplines.about%TYPE);
CREATE FUNCTION ADD_PROFDISC(IN disciplines.name%TYPE,
			IN INTEGER[],
			IN disciplines.lessons%TYPE,
			IN disciplines.practices%TYPE,
			IN disciplines.labs%TYPE,
			IN disciplines.courseovik%TYPE,
			IN disciplines.control%TYPE,
			IN disciplines.about%TYPE)
RETURNS VOID AS $$
BEGIN
	PERFORM ADDDISC($1, $2, $3, $4, $5, $6, $7, $8);
	UPDATE disciplines SET prof = TRUE WHERE name = $1;
END;
$$ LANGUAGE plPGSQL;


--
--
--

SELECT ADD_PROFDISC('Математический анализ', ARRAY[1,2], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Линейная алгебра и аналитическая геометрия', ARRAY[1,2], 0, 0, 0, 'нет' ,'Экзамен','');
SELECT ADD_PROFDISC('Дискретная математика', ARRAY[1], 0, 0, 0, 'нет','Экзамен','');
SELECT ADD_PROFDISC('Теория вероятностей и математическая статистика', ARRAY[2], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Теория случайных процессов', ARRAY[3], 0, 0, 0, '', 'Экзамен','');
SELECT ADD_PROFDISC('Теория функций комплексного переменного', ARRAY[2], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Дифференциальные уравнения', ARRAY[2], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Вариационное исчисление', ARRAY[3], 0, 0, 0, 'есть', 'Зачет','');
SELECT ADD_PROFDISC('Уравнения в частных производных', ARRAY[3], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Математическое моделирование', ARRAY[3,4], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Алгоритмические языки и программирование', ARRAY[1,2], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Базы данных', ARRAY[5], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Компьютерная графика', ARRAY[3], 0, 0, 0, 'есть', 'Экзамен','');
SELECT ADD_PROFDISC('Алгоритмика', ARRAY[4], 0, 0, 0, 'нет', 'Зачет','');
SELECT ADD_PROFDISC('Архитектура ЭВМ', ARRAY[2,3], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Основы теории информационных сетей', ARRAY[5], 0, 0, 0, 'нет', 'Зачет','');
SELECT ADD_PROFDISC('Теория оптимизации', ARRAY[3], 0, 0, 0, 'нет', 'Зачет','');
SELECT ADD_PROFDISC('Численные методы', ARRAY[3], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Теория управления', ARRAY[5], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Теория игр', ARRAY[4], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Интеллектуальные системы', ARRAY[5], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Математические модели сигналов', ARRAY[3], 0, 0, 0, 'нет', 'Зачет','');
SELECT ADD_PROFDISC('Функциональный анализ', ARRAY[3], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Основы теории систем', ARRAY[4], 0, 0, 0, 'нет', 'Экзамен','');
SELECT ADD_PROFDISC('Методы и средства визуального проектирования', ARRAY[3], 0, 0, 0, 'нет', 'Зачет','');
SELECT ADD_PROFDISC('Программное обеспечение систем обмена данными', ARRAY[4], 0, 0, 0, '', 'Экзамен','');

