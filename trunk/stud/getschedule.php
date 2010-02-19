<?php
	include_once('include/lib.php');

	$db = db_connect();
	if (!$db)
		include_once('include/html_db_error.php');

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

	if(isset($_GET['week'])){
		$group = pg_escape_string($_GET['group']);
		$week = pg_escape_string($_GET['week']);
		$data = get_data($db,$week,$group);

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
			echo "</table>";
			echo "<a href=\"#\" onclick=\"dynPageObj.setUrl('getschedule.php?week=".($week+1)."&group=".$group."');dynPageObj.loadPage();this.style.display='none';return false\">Next week</a> ";

		}else{
			echo "<td><h1>Сессия</h1></td>";
			echo "</table>";
		}

		echo "<table>";
		echo "<tr><td><td><td></td></td></td></tr>";
		echo "</table>";

	}
	pg_close($db);
	
?>
