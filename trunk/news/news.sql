--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: news; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

DROP TABLE IF EXISTS news;
CREATE TABLE news (
    news_id SERIAL PRIMARY KEY,
    news_date date,
    author_id integer,
    type_id integer,
    headline character varying(100),
    news_desc text
);


--
-- Name: type_of_news; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

DROP TABLE IF EXISTS type_of_news;
CREATE TABLE type_of_news (
    type_id integer NOT NULL,
    type_desc character varying(70)
);


COPY type_of_news (type_id, type_desc) FROM stdin;
1	Изменения в расписании
2	Новости деканата
3	Новости библиотеки
4	Объявление
5	Другое
\.


