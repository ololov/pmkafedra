--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- Name: seq_news_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE seq_news_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.seq_news_id OWNER TO postgres;

--
-- Name: seq_news_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('seq_news_id', 1, true);


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: news; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE news (
    news_id integer DEFAULT nextval('seq_news_id'::regclass) NOT NULL,
    news_date date,
    author_id integer,
    type_id integer,
    headline character varying(100),
    news_desc text
);


ALTER TABLE public.news OWNER TO postgres;

--
-- Name: type_of_news; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE type_of_news (
    type_id integer NOT NULL,
    type_desc character varying(70)
);


ALTER TABLE public.type_of_news OWNER TO postgres;

--
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY news (news_id, news_date, author_id, type_id, headline, news_desc) FROM stdin;
\.


--
-- Data for Name: type_of_news; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY type_of_news (type_id, type_desc) FROM stdin;
1	Изменения в расписании
2	Новости деканата
3	Новости библиотеки
4	Объявление
5	Другое
\.


--
-- Name: pkey_news_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY news
    ADD CONSTRAINT pkey_news_id PRIMARY KEY (news_id);


--
-- Name: pkey_type_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY type_of_news
    ADD CONSTRAINT pkey_type_id PRIMARY KEY (type_id);


--
-- Name: foreign_type; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY news
    ADD CONSTRAINT foreign_type FOREIGN KEY (type_id) REFERENCES type_of_news(type_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: seq_news_id; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON SEQUENCE seq_news_id FROM PUBLIC;
REVOKE ALL ON SEQUENCE seq_news_id FROM postgres;
GRANT ALL ON SEQUENCE seq_news_id TO postgres;
GRANT ALL ON SEQUENCE seq_news_id TO dbuser;


--
-- Name: news; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE news FROM PUBLIC;
REVOKE ALL ON TABLE news FROM postgres;
GRANT ALL ON TABLE news TO postgres;
GRANT ALL ON TABLE news TO dbuser;


--
-- Name: type_of_news; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE type_of_news FROM PUBLIC;
REVOKE ALL ON TABLE type_of_news FROM postgres;
GRANT ALL ON TABLE type_of_news TO postgres;
GRANT ALL ON TABLE type_of_news TO dbuser;


--
-- PostgreSQL database dump complete
--

