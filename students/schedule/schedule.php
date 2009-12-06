<?php 
	include_once("./students/s_sidebar.php"); 

	/*
	* Получение номера курса и группы
	* По умолчанию будет выводиться первый курс и первая группа
	*/
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
	/*
	* 
	*/
	$db = pg_pconnect("dbname=clericsu_pm host=localhost user=postgres") or die(pg_last_error());
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
		$query = "SELECT para, predmet, lname, fname, sname, ttype, ddate, auditoriya, ggroup FROM schedule_table WHERE ddate >= '".$interval[0]."' AND ddate <= '".$interval[1]."' AND  ggroup = '".$group."' ORDER BY ddate";
		$result = pg_query($db,$query) or die(pg_last_error());
		return  pg_fetch_all($result);
	}

	$group = $course."-".$group;
?>
<script language = "javascript">
	/*
	* Запись чего либо в cookie, в данном срипте запись номера недели, счет идет
	* от текущей недели года.
	*/
	function setCookie(name, value, path, domain, expires, secure){
		var today = new Date();
		today.setTime(today.getTime());

	        if(expires){
			 expires = expires * 1000 * 60 * 60 * 24;
		}
		var expires_date = new Date( today.getTime() + (expires) );
		document.cookie = name + "=" +escape( value ) + ( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) + ( ( path ) ? ";path=" + path : "" ) + ( ( domain ) ? ";domain=" + domain : "" ) + ( ( secure ) ? ";secure" : "" );

	}
	/*
	* Получение данных из cookie
	*/
	function getCookie(name){
		var cook = document.cookie;
		var pos = cook.indexOf(name + '=');
		if(pos == -1){
			return null;
		} else {
			var pos2 = cook.indexOf(';', pos);
			if(pos2 == -1)
				return unescape(cook.substring(pos + name.length + 1));
			else 
				return unescape(cook.substring(pos + name.length + 1, pos2));
		}
	}

	/*
	* Собственно функции отвечающие за реакцию на нажатии кнопок
	*/
	function Next(){
		var week = getCookie('week');
		week++;
		setCookie('week',week);
		document.myform.submit();
	}
	function Last(){
		var week = getCookie('week');
		week--;
		setCookie('week',week);
		document.myform.submit();
	}
</script>

<div id = "main">
	<p class = "tit"> Расписание занятий </p>
	<form name="myform" method="POST" id = "sch_form">
		<input type="hidden" name="week" value="">
		<input type="button" onClick = "Last()" value = "<< назад"  id = "last">
		<input type="button" onClick = "Next()" value = "вперед >>" id = "next">
	</form>


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
			if(isset($_COOKIE['week'])){
				$week = $_COOKIE['week'];
			}else{
				setcookie('week', 0);
			}
			$data = get_data($db,$week,$group);
			/*
			* Извлечение из полученных данных уникальных дат
			*/
			$date = array();
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
						echo		"<p class = prep>".$dt['lname']." ".$dt['fname']." ".$dt['sname']."</p></td>";
						echo 		"<td id = sch_type>".$dt['ttype']."</td>";
						echo 		"<td id = sch_audt>".$dt['auditoriya']."</td>";
						echo "</tr>";
					}
				}
			}
			pg_close($db);
		?>
		</table>
	</div>
</div>
