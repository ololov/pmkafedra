<?php
	include_once('include/lib.php');

	$db = db_connect();
	if (!$db)
		include_once('include/html_db_error.php');

	$limit = 25;
	$limit_page = 10;

	function tag_href($ref, $label)
	{
	        return "<a href=\"$ref\">$label</a>";
	}

	function get_max_page(){
		global $group, $db;
		$query="SELECT max(ddate) FROM schedule_table WHERE ggroup='$group'";
		$res = pg_query($db, $query);
		if(!$res)
			include_once('include/html_db_error.php');
		$result = pg_fetch_row($res);
		$cur_date = strftime("%U", strtotime($result[0]));
		return $cur_date;
	}

	function get_page_list($curr, $max)
	{
		global $limit, $limit_page;
		$curr_url = $_SERVER['REQUEST_URI'];
		$curr_url = mb_eregi_replace("\&?pg=[0-9]+", '', $curr_url);

		/* check left bound */
		$side = (int)($limit_page / 2);

		$left = $curr - $side + 1;
		$right = $curr + $side;
		$diff = $right - $left;

		if ($max >= $limit_page) {
			if ($left < 1) {
				$left = 1;
				$right = $limit_page;
			} else if ($right > $max) {
				$right = $max;
				$left = $max - $limit_page + 1;
			}
		} else {
			$left = 1;
			$right = $max;
		}

		$ldots = ($left == 1) ? ('') : ('... ');
		$rdots = ($right == $max) ? ('') : ('...');

		$res = "<div id=\"pagelist\">$ldots";
		for ($i = $left; $i <= $right; ++$i){
			if ($i == $curr) {
				$res .= "$i ";
			} else {
				$tmp = tag_href($curr_url . htmlspecialchars("&pg=$i"), "$i ");
				$res .= str_replace(htmlspecialchars("shedule.php&"), "shedule.php?", $tmp);
			}
		}
		$res .= " $rdots</div>";
		return $res;	
	}

	date_default_timezone_set('Europe/Moscow');
	/*
	* Получение даты начала недели и конца недели по номеру недели в году
	* просвещенные знают зачем она нужна =))
	*/
	function get_interval_week($num_week,$db){
		$cur_date = strftime("%U", strtotime("$num_week week"));
		$res = pg_query($db,"SELECT get_interval_week(".date('Y').",$cur_date)");
		$result = pg_fetch_row($res);
		return explode("---", $result[0]);
	}
	/*
	* Плучение данных из БД удовлетворяющие запросу
	*/
	function get_data($db, $num_week=0, $group){
		$interval = get_interval_week($num_week,$db);
		$query = "SELECT para, predmet, worker_name, ttype, ddate, auditoriya, ggroup FROM schedule_table WHERE ddate >= '".$interval[0]."' AND ddate <= '".$interval[1]."' AND  ggroup = '".$group."' ORDER BY ddate";
		$result = pg_query($db,$query) or die(pg_last_error());
		return  pg_fetch_all($result);
	}


	function print_schedule(){
		global $db, $group, $page;

		$data = get_data($db,($page-1),$group);

		$DaysOfWeek = array("Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота");

		$date = array();
		echo "<table id=\"schedule\"";
		if(!empty($data)){
			/*
			* Извлечение из полученных данных уникальных дат
			*/
			foreach($data as $dt){
				if(in_array($dt['ddate'],$date)) continue;
				else $date[] = $dt['ddate'];
			}

			for ($j = 0; $j < count($date); $j++) {
				$num_day = strftime("%w",strtotime($date[$j]));
				echo "<td id = sch_day colspan = 2> $DaysOfWeek[$num_day] </td>";
				echo "<td id = sch_num colspan = 2> $date[$j] </td> <tr>";
				foreach($data as $dt){
					if($date[$j] === $dt['ddate']){
						echo "<tr id = sch_main>";
						echo 		"<td id = sch_para>".$dt['para']."</td>";
						echo		"<td id = sch_cont><p class = pred>".$dt['predmet']."</p>";
						echo		"<p class = prep>".$dt['worker_name']."</p></td>";
						echo 		"<td id = sch_type>".$dt['ttype']."</td>";
						echo 		"<td id = sch_audt>".$dt['auditoriya']."</td>";
						echo "</tr>";
					}
				}
			}

		}else{
			echo "<td><h1>Сессия</h1></td>";
		}
		echo "</table>";
	}

?>
