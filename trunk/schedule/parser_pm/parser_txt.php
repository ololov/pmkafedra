<?php
	$db = pg_pconnect("dbname=clericsu_pm host=localhost user=postgres") or die(pg_last_error());

	date_default_timezone_set('Europe/Moscow');
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

	function myexplode($str){
		$str_array = explode(":",$str);
		$str_array[count($str_array)-1] = parser_date(end($str_array));
		return $str_array;
	}

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

	function data($data, $num){
		$data_array = array();
		if(is_array($data) && is_numeric($num)){
			foreach($data as $file){
				foreach($file as $schedule){
					$data_array[] = $schedule[$num];
				}
			}	
			$data_array = array_unique($data_array);
			sort($data_array);
			return $data_array;
		}
	}

	function add_data_to_db($data,$db){
		foreach($data as $key=>$dt){
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
