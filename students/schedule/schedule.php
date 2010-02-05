<?php 
	include_once("./students/s_sidebar.php"); 
	require_once('./include/logins.php');
	require_once('./include/lib.php');

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

	$group = $course."-".$group;
?>
<script type="text/javascript" src="students/schedule/js/ajax.js"></script>
<script type="text/javascript" src="students/schedule/js/ajax-dynamic-content.js"></script>
<script type="text/javascript" src="students/schedule/js/ajax-dynamic-pages.js"></script>

<div id = "main">
	<p class = "tit"> Расписание занятий </p>
<!--	<form name="myform" method="POST" id = "sch_form">
		<input type="button" onClick = "Last()" value = "<< назад"  id = "last">
		<input type="button" onClick = "Next()" value = "вперед >>" id = "next">
	</form> -->

	<div id = "group">
		<ul>
			<li><a href = "?page=stud&amp;dep=sched&amp;course=<?php echo $course ?>&amp;group=1">Группа 1</a></li>
			<li><a href = "?page=stud&amp;dep=sched&amp;course=<?php echo $course ?>&amp;group=2">Группа 2</a></li>
		<ul>
	</div>
	<div>
		<table>
		<tr><td></td></tr>
		</table>
	</div>
	<div id = "sch">

	</div>

</div>
	<script type="text/javascript">
		var dynPageObj = new DHTMLgoodies_scrollingPages();
		dynPageObj.setTargetId('sch');
		dynPageObj.setUrl('students/schedule/getschedule.php?week=0&group=<?php echo $group?>');
		dynPageObj.setScrollSpeed(20);
		dynPageObj.loadPage();
	</script>

