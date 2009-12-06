SET check_function_bodies=true;
SET client_encoding='UTF8';
--
--******************* MY TYPE *************************
--

CREATE TYPE semestr_val AS ENUM ('1','2','3','4','5','6','7','8','9','10','11');
CREATE TYPE control_form_val AS ENUM ('Экзамен', 'Диф.Зачет', 'Зачет');
CREATE TYPE type_val AS ENUM ('Лекция','Пр.Зан.','Семинар');
--
--****************** END MY TYPE **********************
--


--
--****************** TABLES ****************************
--
DROP TABLE IF EXISTS prepod CASCADE;
DROP SEQUENCE IF EXISTS prepod_seq;
CREATE SEQUENCE prepod_seq;
CREATE TABLE prepod(
	id SERIAL PRIMARY KEY,
	fname varchar(30) NOT NULL,
	sname varchar(30) NOT NULL,
	lname varchar(40) NOT NULL,
	post text NOT NULL,
	scentific_int text NOT NULL,
	contact varchar(30),
	kafedra text
);

DROP TABLE IF EXISTS predmet_info CASCADE;
DROP SEQUENCE IF EXISTS predmet_info_seq;
CREATE SEQUENCE predmet_info_seq;
CREATE TABLE predmet_info(
	id SERIAL PRIMARY KEY,
	predmet_name varchar(160) NOT NULL,
	prepod integer REFERENCES prepod(id),
	semestr semestr_val,
	control_form control_form_val
);

DROP TABLE IF EXISTS schedule CASCADE;
DROP SEQUENCE IF EXISTS schedule_seq;
CREATE SEQUENCE schedule_seq;
CREATE TABLE schedule(
	id SERIAL PRIMARY KEY,
	predmet integer REFERENCES predmet_info (id),
	prepod integer  REFERENCES prepod(id),
	ggroup varchar(5) NOT NULL,
	ttype type_val NOT NULL
);

DROP TABLE IF EXISTS other CASCADE;
CREATE TABLE other(
	predmet integer REFERENCES schedule(id),
	ddate date NOT NULL,
	para semestr_val NOT NULL,
	auditoriya varchar(13)
);
--
--***************** END TABLES *****************************
--

-- Функция добавления в БД всякий даных, типа предмета, 
-- группы, типа(лекция, пр.зан. и тд) и тд.
CREATE OR REPLACE FUNCTION add_datas(varchar, 
				     type_val, 
				     varchar, 
				     varchar, 
				     varchar, 
				     varchar) 
RETURNS integer AS $$
DECLARE
	t_predmet ALIAS FOR $1;
	t_ttype ALIAS FOR $2;
	t_ggroup ALIAS FOR $3;
	t_lname ALIAS FOR $4;
	t_fname ALIAS FOR $5;
	t_sname ALIAS FOR $6;

	t_id_prepod integer;
	t_id_predmet integer;
	t_id_schedule integer;

BEGIN
	SELECT INTO t_id_prepod id FROM prepod WHERE (substr(fname,1,1) = t_fname AND lname = t_lname AND substr(sname,1,1) = t_sname);
	IF NOT FOUND THEN
		RAISE EXCEPTION 'ERROR! IN TABLE prepod NOT FOUND ZAPPISES';
		RETURN -1;
	END IF;

	SELECT id,predmet_name,prepod INTO t_id_predmet FROM predmet_info WHERE (predmet_name = t_predmet AND prepod = t_id_prepod);
	IF NOT FOUND THEN
		INSERT INTO predmet_info (predmet_name, prepod) VALUES (t_predmet,t_id_prepod) RETURNING id INTO t_id_predmet;
	END IF;
	INSERT INTO schedule (predmet, prepod, ggroup, ttype) VALUES(t_id_predmet,t_id_prepod,t_ggroup,t_ttype) RETURNING id INTO t_id_schedule;

	RETURN t_id_schedule;
END;
$$ LANGUAGE 'plpgsql';

--
--***************** VIEW *****************************
--
DROP VIEW IF EXISTS schedule_table;
CREATE VIEW schedule_table AS 
SELECT ot.para para, p.predmet_name predmet, pr.lname lname, pr.fname fname, pr.sname  sname, 
sch.ttype ttype, ot.ddate  ddate, ot.auditoriya  auditoriya, sch.ggroup ggroup
FROM prepod pr INNER JOIN predmet_info p ON pr.id = p.prepod
               INNER JOIN schedule sch ON sch.predmet = p.id
	       INNER JOIN other ot ON ot.predmet = sch.id ORDER BY ot.ddate;

--
--
--

CREATE OR REPLACE FUNCTION get_interval_week(IN jahr int, IN kw int) RETURNS text AS $$
DECLARE
	datum date;
	ret text;
BEGIN
	datum = (jahr || '-01-01')::date;
	LOOP
		EXIT WHEN EXTRACT(dow from datum) = 4;
		datum = datum + '1 day'::interval;
	END LOOP;
	ret = to_char(datum + (7*(kw)-3 || 'days')::interval,'yyyy-mm-dd') || '---' || to_char(datum + (3+7*(kw)||'days')::interval,'yyyy-mm-dd');
	RETURN ret;
END;
$$ LANGUAGE 'plpgsql';
