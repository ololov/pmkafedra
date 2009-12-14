SET check_function_bodies=true;
SET client_encoding='UTF8';
--
--* * * * * T A B L E S * * * * * *
--
DROP TABLE IF EXISTS fr_idrismoder CASCADE;
CREATE TABLE fr_idrismoder (
    id_user integer NOT NULL,
    id_theme integer NOT NULL
);

DROP SEQUENCE IF EXISTS id_message CASCADE;
CREATE SEQUENCE id_message
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

SELECT pg_catalog.setval('id_message', 10, true);

DROP TABLE IF EXISTS fr_message CASCADE;
CREATE TABLE fr_message (
    id integer DEFAULT nextval('id_message'::regclass) NOT NULL,
    txt text,
    header character varying(45) DEFAULT NULL::character varying,
    "time" timestamp without time zone DEFAULT now() NOT NULL,
    idrthread integer NOT NULL,
    idrauthor integer NOT NULL
);

DROP SEQUENCE IF EXISTS id_role CASCADE;
CREATE SEQUENCE id_role
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

SELECT pg_catalog.setval('id_role', 1, false);

DROP TABLE IF EXISTS fr_role CASCADE;
CREATE TABLE fr_role (
    id integer DEFAULT nextval('id_role'::regclass) NOT NULL,
    name character varying(45) DEFAULT NULL::character varying
);

DROP SEQUENCE IF EXISTS id_theme CASCADE;
CREATE SEQUENCE id_theme
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SELECT pg_catalog.setval('id_theme', 1, false);

DROP TABLE IF EXISTS fr_theme CASCADE;
CREATE TABLE fr_theme (
    id integer DEFAULT nextval('id_theme'::regclass) NOT NULL,
    name character varying(45) NOT NULL,
    comment character varying(45) DEFAULT NULL::character varying
);

DROP SEQUENCE IF EXISTS id_thread CASCADE;
CREATE SEQUENCE id_thread
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


SELECT pg_catalog.setval('id_thread', 16, true);

DROP TABLE IF EXISTS fr_thread CASCADE;
CREATE TABLE fr_thread (
    id integer DEFAULT nextval('id_thread'::regclass) NOT NULL,
    name character varying(45) NOT NULL,
    comment character varying(100) DEFAULT NULL::character varying,
    idrstarter integer NOT NULL,
    idrtheme integer NOT NULL
);

DROP SEQUENCE IF EXISTS id_user CASCADE;
CREATE SEQUENCE id_user
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

SELECT pg_catalog.setval('id_user', 27, true);

DROP TABLE IF EXISTS fr_user CASCADE;
CREATE TABLE fr_user (
    id integer DEFAULT nextval('id_user'::regclass) NOT NULL,
    name character varying(45) NOT NULL,
    nickname character varying(45) NOT NULL,
    signature character varying(45) DEFAULT NULL::character varying,
    idrrole integer NOT NULL
);

DROP VIEW IF EXISTS main_page;
CREATE VIEW main_page AS
    SELECT fr_theme.id, fr_theme.name, fr_theme.comment, (SELECT count(*) AS count FROM fr_thread WHERE (fr_thread.idrtheme = fr_theme.id)) AS thread_count, (SELECT count(*) AS count FROM fr_message WHERE (fr_message.idrthread IN (SELECT fr_thread.id FROM fr_thread WHERE (fr_thread.idrtheme = fr_theme.id)))) AS message_count, (SELECT fr_message."time" FROM fr_message WHERE (fr_message."time" = (SELECT max(fr_message."time") AS max FROM fr_message WHERE (fr_message.idrthread IN (SELECT fr_thread.id FROM fr_thread WHERE (fr_thread.idrtheme = fr_theme.id)))))) AS last_msg_time, (SELECT (SELECT fr_user.nickname FROM fr_user WHERE (fr_user.id = fr_message.idrauthor)) FROM fr_message WHERE (fr_message."time" = (SELECT max(fr_message."time") AS max FROM fr_message WHERE (fr_message.idrthread IN (SELECT fr_thread.id FROM fr_thread WHERE (fr_thread.idrtheme = fr_theme.id)))))) AS last_msg_author FROM fr_theme;

DROP VIEW IF EXISTS theme_page;
CREATE VIEW theme_page AS
    SELECT fr_thread.id, fr_thread.name, fr_thread.comment, (SELECT count(*) AS count FROM fr_message WHERE (fr_message.idrthread = fr_thread.id)) AS message_count, (SELECT fr_user.nickname FROM fr_user WHERE (fr_user.id = fr_thread.idrstarter)) AS topic_starter, fr_thread.idrtheme FROM fr_thread;

DROP VIEW IF EXISTS thread_page;
CREATE VIEW thread_page AS
    SELECT fr_message.id, fr_message.txt, fr_message.header, (SELECT fr_user.nickname FROM fr_user WHERE (fr_user.id = fr_message.idrauthor)) AS author, (SELECT fr_user.signature FROM fr_user WHERE (fr_user.id = fr_message.idrauthor)) AS sign, fr_message.idrthread, fr_message.idrauthor FROM fr_message;


INSERT INTO fr_role (id, name) VALUES (DEFAULT, 'user');
INSERT INTO fr_theme (id, name, comment) VALUES (DEFAULT, 'Учебный процесс', 'Для обсуждения орг. вопросов');
INSERT INTO fr_theme (id, name, comment) VALUES (DEFAULT, 'Решения', 'Помощь в решении задач');
INSERT INTO fr_theme (id, name, comment) VALUES (DEFAULT, 'Флудилка', '=)');

ALTER TABLE ONLY fr_idrismoder
    ADD CONSTRAINT fr_idrismoder_pkey PRIMARY KEY (id_user, id_theme);

ALTER TABLE ONLY fr_message
    ADD CONSTRAINT fr_message_pkey PRIMARY KEY (id);

ALTER TABLE ONLY fr_thread
    ADD CONSTRAINT fr_thread_pkey PRIMARY KEY (id);

ALTER TABLE ONLY fr_user
    ADD CONSTRAINT fr_user_pkey PRIMARY KEY (id);

ALTER TABLE ONLY fr_role
    ADD CONSTRAINT role_pkey PRIMARY KEY (id);

ALTER TABLE ONLY fr_theme
    ADD CONSTRAINT theme_pkey PRIMARY KEY (id);

ALTER TABLE ONLY fr_message
    ADD CONSTRAINT fk_message_thread1 FOREIGN KEY (idrthread) REFERENCES fr_thread(id);

ALTER TABLE ONLY fr_message
    ADD CONSTRAINT fk_message_user1 FOREIGN KEY (idrauthor) REFERENCES fr_user(id);

ALTER TABLE ONLY fr_thread
    ADD CONSTRAINT fk_thread_theme1 FOREIGN KEY (idrtheme) REFERENCES fr_theme(id);

ALTER TABLE ONLY fr_thread
    ADD CONSTRAINT fk_thread_user1 FOREIGN KEY (idrstarter) REFERENCES fr_user(id);

ALTER TABLE ONLY fr_idrismoder
    ADD CONSTRAINT fk_user_has_theme_theme1 FOREIGN KEY (id_theme) REFERENCES fr_theme(id);


ALTER TABLE ONLY fr_idrismoder
    ADD CONSTRAINT fk_user_has_theme_user1 FOREIGN KEY (id_user) REFERENCES fr_user(id);

ALTER TABLE ONLY fr_user
    ADD CONSTRAINT fk_user_role FOREIGN KEY (idrrole) REFERENCES fr_role(id);

