CREATE TYPE semestr_val AS ENUM ('1','2','3','4','5','6','7','8','9','10','11');
CREATE TYPE control_form_val AS ENUM ('Экзамен', 'Диф.Зачет', 'Зачет');
CREATE TYPE type_val AS ENUM ('Лекция','Пр.Зан.','Семинар');

CREATE SEQUENCE prepod_seq;
CREATE TABLE prepod(
	id integer UNIQUE PRIMARY KEY DEFAULT nextval('prepod_seq'),
	fname varchar(30) NOT NULL,
	sname varchar(30) NOT NULL,
	lname varchar(40) NOT NULL,
	post text NOT NULL,
	scentific_int text NOT NULL,
	contact varchar(30),
	kafedra text
);

CREATE SEQUENCE predmet_info_seq;
CREATE TABLE predmet_info(
	id integer UNIQUE PRIMARY KEY DEFAULT nextval('predmet_info_seq'),
	predmet_name varchar(160) NOT NULL,
	prepod integer REFERENCES prepod(id),
	semestr semestr_val,
	control_form control_form_val
);

CREATE SEQUENCE schedule_seq;
CREATE TABLE schedule(
	id integer UNIQUE PRIMARY KEY DEFAULT nextval('schedule_seq'),
	predmet integer REFERENCES predmet_info (id),
	prepod integer  REFERENCES prepod (id),
	ggroup varchar(5) NOT NULL,
	ttype type_val NOT NULL
);

CREATE TABLE other(
	predmet integer REFERENCES schedule (id),
	ddate date NOT NULL,
	para semestr_val NOT NULL,
	auditoriya varchar(13)
);


