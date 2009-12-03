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
		RAISE EXCEPTION 'ERROR! IN TABLE prepod NOT FOUND ZAPPISES';-- || t_lname || " " || t_fname || " " || t_sname;
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
