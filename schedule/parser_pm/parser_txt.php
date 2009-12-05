<?php
	$db = pg_pconnect("dbname=clericsu_pm host=localhost user=postgres") or die(pg_last_error());

	date_default_timezone_set('Europe/Moscow');
	/*
	* Эта функция разбивает строки типа "только 11.09;12.10" или "с 03.09 по 02.10"
	* на массив дат
	*/
	function parser_date($str){
		$year = date('Y',time());

		if(stripos($str,"только") !== false){
			$str = trim(str_replace("только"," ",$str));
			$array = explode(";",$str);
			foreach($array as &$val){
				$val = date('Y-m-d',strtotime($val.".".$year));
			}

		}elseif(stripos($str,"кроме") !== false){
			$tmp_array = explode("кроме", $str);
			$str = trim(str_replace("с"," ",$tmp_array[0]));
			list($first, $end) = explode("по", $str);
			$date_other = explode(";",trim($tmp_array[1]));
			foreach($date_other as &$dt) $dt .= ".".$year;

			$time_start = strtotime(trim($first).".".$year);
			$time_end = strtotime(trim($end).".".$year);
			
			while($time_end >= $time_start){
				$tmp = date('d.m.Y', $time_start);
				if(!in_array($tmp,$date_other)){
					$array[] = date('Y-m-d',$time_start);
				}
				$time_start = strtotime("$tmp + 1 week");
			}

		}else{
			$str = trim(str_replace("с"," ",$str));
			list($first, $end) = explode("по", $str);

			$time_start = strtotime(trim($first).".".$year);
			$time_end = strtotime(trim($end).".".$year);

			while($time_end >= $time_start){
				$tmp = date('d.m.Y', $time_start);
				$array[] = date('Y-m-d',$time_start);
				$time_start = strtotime("$tmp + 1 week");
			}


		}

		return $array;
	}
	/*
	* Чтобы не перепписывать полностью парсер пришлось ввести эту функцию
	* ее смысл заключается в отыскании верхней и нижний недель.
	*/
	function del_inter($data){
		for($i = 0; $i < count($data); $i++){
			for($j = $i+1; $j < count($data); $j++){
				$tmp = array_uintersect($data[$i][5], $data[$j][5], "strcasecmp");
				if($data[$i][0] == $data[$j][0]){
					if(count($data[$i][5]) == count($data[$j][5])){
						$k = 0;
						foreach($tmp as $dt){
							if($k % 2 === 0){
								$key = array_search($dt, $data[$i][5],true);
								unset($data[$i][5][$key]);
								$k++;
							}else{
								$key = array_search($dt, $data[$j][5],true);
								unset($data[$j][5][$key]);
								$k++;
							}
						}
					
					}elseif(count($data[$i][5]) >= count($data[$j][5])){
						foreach($tmp as $dt){
							$key = array_search($dt, $data[$i][5],true);
							unset($data[$i][5][$key]);
						}
					}else{
						foreach($tmp as $dt){
							$key = array_search($dt, $data[$j][5],true);
							unset($data[$j][5][$key]);
						}

					}
				}

			}
		}

		return $data;
	}

	function myexplode($str){
		$str_array = explode(":",$str);
		$str_array[count($str_array)-1] = parser_date(end($str_array));
		return $str_array;
	}

	/*
	* Небольшая функция реализующая парсер файлов хранящихся в
	* schedule/parser_txt
	*/
	function parser_txtfiles($files = "*.txt"){
		if(is_string($files)){
			$file_list = glob($files);
			foreach($file_list as $file){
				$index = substr($file,2,3);
				$file_array[$index] = array_map("myexplode",file($file));
			}
			return $file_array;
		}
	}

	/*
	* Занесение все данных в БД
	*/
	function add_data_to_db($data,$db){
		foreach($data as $key=>$dt){
			$dt = del_inter($dt);
			foreach($dt as $val){
				$fio_prepod = explode(" ",substr_replace($val[3],"",stripos($val[3],","),strlen($val[3])));
				if($fio_prepod[0] === "") {
					$fio_prepod[0] = "Неизвестен";
					$fio_prepod[1] = "Х.Х.";
				}
				$inic = explode(".",$fio_prepod[1]);
				$query = "'".$val[1]."','".$val[2]."','".$key."','".$fio_prepod[0]."','".$inic[0]."','".$inic[1]."'";
				$query = "SELECT add_datas(".$query.")";
				$res = pg_query($db,$query) or die(pg_last_error());
				$tmp = pg_fetch_row($res);
				$result = $tmp[0];
				foreach($val[5] as $date ){
					$query = "INSERT INTO other(predmet,ddate,para,auditoriya) VALUES (".$result.",'".$date."','".$val[0]."','".$val[4]."')";
					pg_query($db,$query) or die(pg_last_error());

				}

			}
		}
	}
	$data = parser_txtfiles();

	add_data_to_db($data,$db);

	pg_close($db);

?>
