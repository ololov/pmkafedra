
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
