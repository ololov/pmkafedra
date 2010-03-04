
--
-- Сделал типы новостей как отдельная таблица, хотя
-- есть сомнения что это лучше чем просто перечисление.
--

DROP TABLE IF EXISTS news_types CASCADE;
CREATE TABLE news_types (
	type_name VARCHAR PRIMARY KEY
);

DROP VIEW IF EXISTS news_types_tb CASCADE;
CREATE VIEW news_types_tb AS
SELECT
	type_name AS news_type
FROM
	news_types;

DROP TABLE IF EXISTS news CASCADE;
CREATE TABLE news (
	news_date TIMESTAMP PRIMARY KEY,
	ulogin VARCHAR NOT NULL REFERENCES workers(ulogin),
	news_type VARCHAR NOT NULL REFERENCES news_types(type_name),
	header VARCHAR NOT NULL,
	news_desc TEXT NOT NULL
);

DROP VIEW IF EXISTS news_tb CASCADE;
CREATE VIEW news_tb AS
SELECT
	news_date AS news_date,
	news_type AS news_type,
	ulogin AS worker_login,
	header AS news_header,
	news_desc AS news_text
FROM
	news;
--
--

DROP FUNCTION IF EXISTS ADD_NEWS(IN news.ulogin%TYPE, IN news.news_type%TYPE,
				 IN news.header%TYPE, IN news.news_desc%TYPE);
CREATE FUNCTION ADD_NEWS(IN workers.ulogin%TYPE, IN news.news_type%TYPE,
			 IN news.header%TYPE, IN news.news_desc%TYPE)
RETURNS VOID AS $$
BEGIN
	INSERT INTO news(news_date, ulogin, news_type, header, news_desc)
	VALUES (NOW(), $1, $2, $3, $4);
EXCEPTION
	WHEN foreign_key_violation THEN
		RAISE EXCEPTION 'Неизвестный тип новостей или неизвестный сотрудник кафедры.';
END;
$$ LANGUAGE plPGSQL;

INSERT INTO news_types(type_name)
VALUES ('Изменения в расписании'), ('Новости деканата'), ('Новости библиотеки'),
	('Объявления'), ('Другое');

SELECT ADD_NEWS('prepod', 'Объявления', 'Внимание сотрудники кафедры!',
		'Уважаемые сотрудники кафедры! Если вы не нашли о себе информацию на странице о сотрудниках кафедры,
		либо считаете информацию о себе неполной или недостоверной (или вам не нравится ваша фотография) - 
		просьба обратиться к любому из разработчиков сайта, чтобы устранить это недоразумение.');
SELECT ADD_NEWS('prepod', 'Объявления', 'Защита преддипломной практики',
		'С 9 по 12 марта состоится защита преддипломной практики у студентов 5ого курса.');
SELECT ADD_NEWS('prepod','Объявления','Государственные экзамены','Предварительные даты государственных экзаменов:
		24 марта (вечер) - тестирование, 26 марта (весь день) - устный экзамен по математике.');
