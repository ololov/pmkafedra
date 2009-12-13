<?php
	//возвращает идентификатор типа новости
	function get_type_id($type) {
		switch ($type) {
			case "sch" : $i = 1; break;
			case "den" : $i = 2; break;
			case "lib" : $i = 3; break;
			case "adt" : $i = 4; break;
			case "oth" : $i = 5; break;
		}
		return $i;
	}

	//формирует массив для добавления в таблицу бд
	function form_data($auth) {
		$data = array();
		
		$data['news_id']   = "";
		$data['news_date'] = pg_escape_string(date("Y-m-d"));
		$data['author_id'] = $auth;
		$data['type_id']   = get_type_id($_POST['types']);
		$data['headline']  = pg_escape_string($_POST['headline']);
		$data['news_desc'] = pg_escape_string($_POST['message']);
								
		return $data;
	}

	function get_data($query) {
		$r = pg_query($query) or die (pg_last_error());
		for ($data = array(); $row = pg_fetch_assoc($r); $data[] = $row);
		return $data;
	}
	
	function get_str($n) {
		if ($n == 0) $str = "SELECT * FROM news";
		else	     $str = "SELECT * FROM news WHERE type_id = ".$n;
		return $str;
	}
?>
