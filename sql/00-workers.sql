

--
-- Name: prepod; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

DROP TABLE IF EXISTS workers CASCADE;
CREATE TABLE workers (
	ulogin VARCHAR PRIMARY KEY,
	name VARCHAR NOT NULL,
	seat TEXT NOT NULL,
	interests TEXT,
	contact VARCHAR,
	kafedra TEXT,
	about TEXT,
	photo VARCHAR,
	CHECK(name != ''),
	CHECK(seat != ''),
	CHECK(photo != '')
);

DROP VIEW IF EXISTS workers_tb CASCADE;
CREATE VIEW workers_tb AS
SELECT
	ulogin AS worker_login,
	name AS worker_name,
	seat AS worker_seat,
	interests AS worker_interests,
	kafedra AS worker_kafedra,
	about AS worker_about,
	contact AS worker_contact,
	photo AS worker_photo
FROM workers;

--
--
--

INSERT INTO workers(ulogin, name, seat, interests, contact, kafedra, about, photo)
VALUES('prepod', 'Васюткин Василий Васильевич', 'старший преподаватель',
'Азбука', 'god@somewhere.net', 'Кафедра ПМ',
'Родился, жил и умер.', 'photo/tmp.png');

