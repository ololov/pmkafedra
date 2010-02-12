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
CREATE TABLE predmet_info(
	id SERIAL PRIMARY KEY,
	predmet_name varchar(160) NOT NULL,
	prepod integer REFERENCES prepod(id),
	semestr semestr_val,
	control_form control_form_val
);

DROP TABLE IF EXISTS schedule CASCADE;
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
	ret = to_char(datum + (7*(kw-1)-3 || 'days')::interval,'yyyy-mm-dd') || '---' || to_char(datum + (3+7*(kw-1)||'days')::interval,'yyyy-mm-dd');
	RETURN ret;
END;
$$ LANGUAGE 'plpgsql';


--
--* * * * * * * I N S E R T * * * * * * * * 
--
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Кузнецов','Валерий', 'Леонидович', 'Зав. кафедрой, д.т.н., профессор.','методы математического моделирования в задачах распространения излучения в пространственно неоднородных случайных  и периодических средах, безопасность полетов.', 'kuznetsov@mstuca.ru', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Егорова', 'Алла', 'Альбертовна', 'д.т.н., профессор.', 'оптимизация и автоматизация принятия управленческих решений в условиях неполной определенности.', 'ego_alla@mail.ru', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Ерзакова', 'Нина', 'Александровна', 'д.ф.-м.н., профессор.','дифференциальные уравнения с частными производными, функциональный анализ.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Калмыков', 'Георгий', 'Иванович', 'д.ф.-м.н., профессор.', 'теория графов, древесные графы.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Котиков', 'Вячеслав', 'Иванович', 'к.т.н., профессор.', 'электронные информотеки и информационные технологии.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Аль-Натор','Мухаммед', 'Субхи', 'к.ф.-м.н., доцент.', 'актуарная математика, криптография, теория риска, финансовая математика.', 'malnator@yandex.ru', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Аль-Натор', 'Софья', 'Владимировна', 'к.ф.-м.н., доцент.', 'теория массового обслуживания, теория вероятности и математическая статистика, страхование', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Ивенин', 'Игорь', 'Борисович', 'к.т.н., доцент.', 'математические методы системного анализа, исследование операций и обоснование решений.', 'ibi.new@mail.ru', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Коновалов', 'Владимир', 'Михайлович ', 'к.т.н., доцент.','информатика и информационные системы, теория информационных сетей.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Коротков', 'Александр', 'Сергеевич ', 'к.т.н., доцент.', 'информатика и информационные технологии.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Лоссиевская', 'Татьяна','Владимировна', 'к.ф.-м.н., доцент.', 'дифференциальные и интегральные уравнения', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Пичугин', 'Андрей', 'Анатольевич', 'к.т.н., доцент.', 'информатика, управление, моделирование, информационные системы и технологии.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Столяров', 'Андрей', 'Викторович', 'к.ф.-м.н., доцент.', 'мультипарадигмальное программирование; имеет опыт чтения лекционных курсов «Операционные системы», «Системы программирования», «Архитектура ЭВМ и язык ассемблера», «Архитектура ЭВМ и системное программное обеспечение», автор спецкурса «Введение в парадигмы программирования».', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Ивенина', 'Елена', 'Михайловна', 'старший преподаватель.', 'методы математического моделирования в механике полета и исследование операций.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Петрова', 'Людмила', 'Владимировна', 'старший преподаватель.', 'информатика и информационные технологии, компьютерная графика.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Филонов', 'Павел', 'Владимирович', 'аспирант', 'распространение электромагнитных волн в неоднородных средах.', 'filonovpv@gmail.com', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Андреева', 'Татьяна', 'Ильинична', 'старший преподаватель.', 'информатика и информационные технологии.', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Агафонов', 'Альберт', 'Васильевич', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Афанасьева', 'Людмила', 'Константиновна', 'старший преподаватель','', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Беликова', 'Татьяна', 'Павловна', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Домород', 'Елена', 'Викторовна', 'преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Железная', 'Ирина', 'Петровна', 'преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Камзолов', 'Сергей', 'Константинович', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Козлова', 'Зинаида','Игоревна', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Кокотушкин', 'В.','А.', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Корниенко', 'Людмила', 'Геннадьевна', 'доцент',  '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Кузнецов', 'Альберт', 'Андреевич', 'профессор', '', '', 'Кафедра прикладной математики');
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Логачев', 'Виктор', 'Петрович', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Матвеева', 'А.','С.', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Мещерякова', 'Лилия', 'Яковлевна', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Некрасов', 'Сергей','Иванович', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Петрунин', 'Станислав', 'Владимирович', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Пименов', 'Владимир', 'Игоревич', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Родионов', 'Михаил', 'Александрович', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Суворов', 'Н.','А.', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Тищенко', 'В.','И.', 'преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Экзерцева', 'Екатерина', 'Вадимовна', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Неизвестен', 'ХЗ', 'ХЗ', 'ХЗ', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Кишенский', 'Сергей','Жанович', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Акылакунова', 'Ассикат','Киекбаевна', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Шурдукова', 'Т.','И.', 'ассистент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Колесников', 'А.','Н.', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Наумова', 'Татьяна','Владимировна', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Козлов', 'А.','С.', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Соловьева', 'Т.','Л.', 'доцент', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Сокол', 'П.','П.', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Лашин', 'В.','Ю.', 'старший преподаватель', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Артеменко', 'Юрий','П.', 'профессор', '', '', NULL);
INSERT INTO prepod (lname,fname,sname,post,scentific_int,contact,kafedra) VALUES ('Зорина', 'О.','В.', 'старший преподаватель', '', '', NULL);
