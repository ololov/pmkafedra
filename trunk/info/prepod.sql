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

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: prepod; Type: TABLE; Schema: public; Owner: dbuser; Tablespace: 
--

CREATE TABLE prepod (
    id integer NOT NULL,
    fname character varying(30) NOT NULL,
    sname character varying(30) NOT NULL,
    lname character varying(40) NOT NULL,
    post text NOT NULL,
    scentific_int text NOT NULL,
    contact character varying(30),
    kafedra text,
    about text
);


ALTER TABLE public.prepod OWNER TO dbuser;

--
-- Name: prepod_id_seq; Type: SEQUENCE; Schema: public; Owner: dbuser
--

CREATE SEQUENCE prepod_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.prepod_id_seq OWNER TO dbuser;

--
-- Name: prepod_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: dbuser
--

ALTER SEQUENCE prepod_id_seq OWNED BY prepod.id;


--
-- Name: prepod_id_seq; Type: SEQUENCE SET; Schema: public; Owner: dbuser
--

SELECT pg_catalog.setval('prepod_id_seq', 39, true);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: dbuser
--

ALTER TABLE prepod ALTER COLUMN id SET DEFAULT nextval('prepod_id_seq'::regclass);


--
-- Data for Name: prepod; Type: TABLE DATA; Schema: public; Owner: dbuser
--

COPY prepod (id, fname, sname, lname, post, scentific_int, contact, kafedra, about) FROM stdin;
12	Андрей	Анатольевич	Пичугин	к.т.н., доцент.	информатика, управление, моделирование, информационные системы и технологии.		Кафедра прикладной математики	Пичугин Андрей Анатольевич родился в 1946 г. в г. Новосибирске. В 1963 году поступил на факультете Автоматики и вычислительной техники Новосибирского электротехнического института, окончил его в 1968 году. После окончания института (1970-1973 г.г.) работал ассистентом, старшим преподавателем кафедры “Прикладная математика” Новосибирского электротехнического института. В 1974 г. поступил в аспирантуру Московского института инженеров гражданской авиации. После окончания аспирантуры в 1976 г. работал младшим научным сотрудником, старшим научным сотрудником в НИС МГТУ ГА. В 1979 г. защитил кандидатскую диссертацию на тему “Разработка и исследование модема с повышенной помехоустойчивостью для абонентских сетей систем обмена данными”. Доцент с 1985 г.\nРаботает в Московском институте инженеров гражданской авиации (МИИ ГА), затем Московском государственном техническом университете гражданской авиации (МГТУ ГА) с 1974 г. В настоящее время доцент кафедры Прикладной математики.\nПичугин А.А. имеет стаж научно-педагогической работы – 38 лет, в том числе стаж педагогической работы в вузе – 34 года. Работает в должности доцента – 26 лет.\nПичугин А.А. – автор 89 печатных трудов (из них 57 научных, 30 учебных и учебно-методических пособий, разработок и работ, 2 авторских свидетельства).
2	Алла	Альбертовна	Егорова	д.т.н., профессор.	оптимизация и автоматизация принятия управленческих решений в условиях неполной определенности.	ego_alla@mail.ru	Кафедра прикладной математики	Егорова Алла Альбертовна в 1983 окончила Московский институт инженеров гражданской авиации (сейчас МГТУ ГА) по специальности инженер-системотехник. С 1993 работает в авиакомпании "Трансаэро", пройдя путь от заместителя центра обучения до начальника отдела управления персоналом.\nОбласть профессиональных интересов: обучение персонала (корпоративное и открытое) и документационное обеспечение процессов управления персоналом.\nЕгорова Алла Альбертовна в нашем университете полностью прошла путь студент - аспирант - кандидат наук - доктор наук.
4	Георгий	Иванович	Калмыков	д.ф.-м.н., профессор.	теория графов, древесные графы.		Кафедра прикладной математики	
30	Лилия	Яковлевна	Мещерякова	доцент			\N	
31	Сергей	Иванович	Некрасов	профессор			\N	
32	Станислав	Владимирович	Петрунин	доцент			\N	
35	Н.	А.	Суворов	старший преподаватель			\N	
37	Екатерина	Вадимовна	Экзерцева	доцент			\N	
38	ХЗ	ХЗ	Неизвестен	ХЗ			\N	
5	Вячеслав	Иванович	Котиков	к.т.н., профессор.	электронные информотеки и информационные технологии.		Кафедра прикладной математики	Котиков Вячеслав Иванович родился в 1941 году. Профессор кафедры прикладной математики, кандидат технических наук, член-корреспондент Международной академии иформатизации. В 1967 году окончил Московский электротехнический институт связи по специальности радиотехник. С 1986 года работает в МГТУ ГА. Автор более 60 учебно-методических пособий и научных работ. Область научных интересов - информационные технологии, создание нового класса информационных систем - электронных информотек, поиск решений по построению живой природы в технических средах.
3	Нина	Александровна	Ерзакова	д.ф.-м.н., профессор.	дифференциальные уравнения с частными производными, функциональный анализ.		Кафедра прикладной математики	\nЕрзакова Нина Александровна родилась в 1954 году в Казахстане. В 1976 году окончила Новосибирский государственный университет. В 1983 году окончила аспирантуру в Воронежском государственном университете, защитив кандидатскую диссертацию "О мерах компактности в банаховых пространствах". С 1989 по 1998 гг. работала доцентом Хабаровского государственного технического университета. В 1998 защитила докторскую диссертацию "Исследование операторов и операторных уравнений, связанное с мерами некомпактности". \nС 1996 года - главный научный сотрудник Хабаровского отделения института прикладной математики ДВО РАН. С 2006 года - профессор МГТУ ГА.\nОпубликовала более 50 научных работ.\nВажнейшими результатами научной деятельности являются доказательсво разрешимости и продолжимости решения задачи Коши для дифференциального уравнения с разрывной правой частью; получение аналога неравенства Эрлинга-Ниренберга, справедливого как для компактных, так и некомпактных операторов вложения пространств Соболева; введение меры некомпактности для правильных пространств.
6	Мухаммед	Субхи	Аль-Натор	к.ф.-м.н., доцент.	актуарная математика, криптография, теория риска, финансовая математика.	malnator@yandex.ru	Кафедра прикладной математики	
10	Александр	Сергеевич 	Коротков	к.т.н., доцент.	информатика и информационные технологии.		Кафедра прикладной математики	
17	Татьяна	Ильинична	Андреева	старший преподаватель.	информатика и информационные технологии.		Кафедра прикладной математики	
18	Альберт	Васильевич	Агафонов	профессор			\N	
19	Людмила	Константиновна	Афанасьева	старший преподаватель			\N	
20	Татьяна	Павловна	Беликова	доцент			\N	
16	Павел	Владимирович	Филонов	аспирант	распространение электромагнитных волн в неоднородных средах.	filonovpv@gmail.com	Кафедра прикладной математики	\nФилонов Павел Владимирович родился в 1985 году в городе Хабаровске. В 2002 году поступил в МГТУ ГА на специальность "Прикладная математика". Окончив университет с отличием, в 2007 году поступил в аспирантуру.\nРаботая в коммерческих структурах, Филонов Павел Владимирович приобрел практический опыт системного администрирования, прикладного и web программирования.\nВо время учебы в аспирантуре получил опыт преподавания различных дисциплин, связанных с информационными технологиями.\nПрофессиональное кредо - если вы не знаете, как что-то сделать, то это не означает, что вы не можете это сделать.\n
21	Елена	Викторовна	Домород	преподаватель			\N	
22	Ирина	Петровна	Железная	преподаватель			\N	
25	В.	А.	Кокотушкин	доцент			\N	
27	Альберт	Андреевич	Кузнецов	профессор			Кафедра прикладной математики	
28	Виктор	Петрович	Логачев	профессор			\N	
33	Владимир	Игоревич	Пименов	старший преподаватель			\N	
34	Михаил	Александрович	Родионов	профессор			\N	
36	В.	И.	Тищенко	преподаватель			\N	
1	Валерий	Леонидович	Кузнецов	Зав. кафедрой, д.т.н., профессор.	методы математического моделирования в задачах распространения излучения в пространственно неоднородных случайных  и периодических средах, безопасность полетов.	kuznetsov@mstuca.ru	Кафедра прикладной математики	\nКузнецов Валерий Леонидович родился в Москве в 1949 году. В 1966 году поступил в МГУ на физический факультет, окончил его в феврале 1972 года. С 1972 по 1974 гг. служил в войсках на базе ПВО. В 1974 году поступилв аспирантуру физического факультета МГУ и 1977 году закончил ее, защитив кандидатскую диссертацию "Исследованиевлияния виртуального катода на распространение возмущений в электронных потоках методами кинетической теории".\nВ период с 1977 по 1981 гг. работал младшим научным сотрудником на кафедре волновых процессов физического факультета МГУ над проблемой создания рентгеновских лазеров. С 1981 по 2000 гг. работал в МГТУ ГА на кафедре физики старшим преподавателем, доцентом. В 2000 году защитил докторскую диссертацию "Теория и методы радиолокационной диагностики состояния открытых каналов распространения радиоволн". С 2000 года по настоящее время профессор, заведующий кафедрой Прикладной мматематики МГТУ ГА.\nИмеет более 100 научных работ, руководит аспирантами кафедры.\nБудучи загруженным административно-производственной деятельностью, имеет маленькое хобби - научная работа над вопросами распространения волн в случайно неоднородных средах и периодических структурах и проблемой безопасности полетов.\nЖизненное кредо - если не знаешь, что делать - сделай первый шаг вперед.\n
7	Софья	Владимировна	Аль-Натор	к.ф.-м.н., доцент.	теория массового обслуживания, теория вероятности и математическая статистика, страхование		Кафедра прикладной математики	
8	Игорь	Борисович	Ивенин	к.т.н., доцент.	математические методы системного анализа, исследование операций и обоснование решений.	ibi.new@mail.ru	Кафедра прикладной математики	
9	Владимир	Михайлович 	Коновалов	к.т.н., доцент.	информатика и информационные системы, теория информационных сетей.		Кафедра прикладной математики	
11	Татьяна	Владимировна	Лоссиевская	к.ф.-м.н., доцент.	дифференциальные и интегральные уравнения		Кафедра прикладной математики	
14	Елена	Михайловна	Ивенина	старший преподаватель.	методы математического моделирования в механике полета и исследование операций.		Кафедра прикладной математики	
15	Людмила	Владимировна	Петрова	старший преподаватель.	информатика и информационные технологии, компьютерная графика.		Кафедра прикладной математики	
23	Сергей	Константинович	Камзолов	профессор			\N	
24	Зинаида	Игоревна	Козлова	старший преподаватель			\N	
26	Людмила	Геннадьевна	Корниенко	доцент			\N	
29	А.	С.	Матвеева	старший преподаватель			\N	
39	Сергей	Жанович	Кишенский	доцент			\N	
13	Андрей	Викторович	Столяров	к.ф.-м.н., доцент.	мультипарадигмальное программирование; имеет опыт чтения лекционных курсов «Операционные системы», «Системы программирования», «Архитектура ЭВМ и язык ассемблера», «Архитектура ЭВМ и системное программное обеспечение», автор спецкурса «Введение в парадигмы программирования».		Кафедра прикладной математики	\nАндрей Викторович Столяров родился в 1974 г. в Москве. В 1992 году поступил на факультет Вычислительной математики и кибернетики МГУ им. М.В.Ломоносова, окончил его в 1997 году, в 1999 году окончил с отличием магистратуру и поступил в аспирантуру, в 2002 году успешно закончил аспирантуру и защитил кандидатскую диссертацию «Интеграция разнородных языковых механизмов в рамках одного языка программирования». В рамках диссертационной работы была создана библиотека InteLib.\nНачиная с 1995 года, А.В.Столяров совмещал учёбу с работой в коммерческих организациях в качестве программиста, в период с 1997 по 2000 год работал системным администратором в провайдинговых компаниях (операторах сети Интернет), в 2001 году вернулся к работе программиста. С января 2002 года А.В.Столяров работает на кафедре Алгоритмических языков ВМиК МГУ; в 2003 году прекратил работу в коммерческих структурах, избрав преподавание в качестве основного вида деятельности.\nС сентября 2007 года работает по совместительству на кафедре прикладной математики МГТУ ГА. В июле 2009 года приказом Рособрнадзора А.В.Столярову присвоено учёное звание доцента по кафедре прикладной математики.
\.


--
-- Name: prepod_pkey; Type: CONSTRAINT; Schema: public; Owner: dbuser; Tablespace: 
--

ALTER TABLE ONLY prepod
    ADD CONSTRAINT prepod_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--
