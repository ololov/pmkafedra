<?php 
	include_once("./students/s_sidebar.php"); 

	if(isset($_GET['course'])){
		$course = pg_escape_string($_GET['course']);
	}else{
		$course = 1;
	}

	if(isset($_GET['group'])){
		$group = pg_escape_string($_GET['group']);
	}else{
		$group = 1;
	}

	$db = pg_pconnect("dbname=clericsu_pm host=localhost user=postgres") or die(pg_last_error());
	date_default_timezone_set('Europe/Moscow');

	function get_interval_week($num_week,$db){
		$cur_date = strftime("%U", strtotime("$num_week week"));
		$res = pg_query($db,"SELECT get_interval_week(".date('Y').",$cur_date)");
		$result = pg_fetch_row($res);
		return explode("---", $result[0]);
	}
	function get_data($db, $num_week, $group){
		$interval = get_interval_week($num_week,$db);
		$query = "SELECT para, predmet, lname, fname, sname, ttype, ddate, auditoriya, ggroup FROM schedule_table WHERE ddate >= '".$interval[0]."' AND ddate <= '".$interval[1]."' AND  ggroup = '".$group."' ORDER BY ddate";
		$result = pg_query($db,$query) or die(pg_last_error());
		return  pg_fetch_all($result);
	}

	$group = $course."-".$group;
	$data = get_data($db,0,$group);
	$date = array();
	foreach($data as $dt){
		if(in_array($dt['ddate'],$date)) continue;
		else $date[] = $dt['ddate'];
	}
	pg_close($db);
?>

<div id = "main">
	<p class = "tit"> Расписание занятий </p>
	<input type="button" style="position:absolute; top:200px; left:300px; height: 20px; width: 100px" value="last week"></input>
	<input type="button" style="position:absolute; top:200px; right:100px; height: 20px; width: 100px" value="next week"></input>


	<div id = "group">
		<ul>
			<li><a href = "?page=stud&amp;dep=sched&amp;course=<?php echo $course ?>&amp;group=1">Группа 1</a></li>
			<li><a href = "?page=stud&amp;dep=sched&amp;course=<?php echo $course ?>&amp;group=2">Группа 2</a></li>
		<ul>
	</div>
	
	<div id = "sch">
		<table id = "schedule">
		<?php
			$DaysOfWeek = array("Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"); 
			for ($j = 0; $j < count($date); $j++) {
				$num_day = strftime("%w",strtotime($date[$j]));
				echo "<td id = sch_day colspan = 2> $DaysOfWeek[$num_day] </td>";
				echo "<td id = sch_num colspan = 2> $date[$j] </td> <tr>";
				foreach($data as $dt){
					if($date[$j] === $dt['ddate']){
						echo "<tr id = sch_main>";
						echo 		"<td id = sch_para>".$dt['para']."</td>";
						echo		"<td id = sch_cont><p class = pred>".$dt['predmet']."</p>";
						echo		"<p class = prep>".$dt['lname']." ".$dt['fname']." ".$dt['sname']."</p></td>";
						echo 		"<td id = sch_type>".$dt['ttype']."</td>";
						echo 		"<td id = sch_audt>".$dt['auditoriya']."</td>";
						echo "</tr>";
					}
				}
			}
		?>
		</table>
	</div>
</div>
