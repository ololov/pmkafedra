<?php include_once("./students/s_sidebar.php"); ?>

<div id = "main">
	<p class = "tit"> Расписание занятий </p>
	<div id = "group">
		<ul>
			<li><a href = #>Группа 1</a></li>
			<li><a href = #>Группа 2</a></li>
		<ul>
	</div>
	
	<div id = "sch">
		<table id = "schedule">
		<?php
			for ($j = 0; $j < 5; $j++) {
				echo "<td id = sch_day colspan = 2> ДЕНЬ НЕДЕЛИ </td>";
				echo "<td id = sch_num colspan = 2> ЧИСЛО </td> <tr>";
				for ($i = 0; $i < 6; $i++) {
					echo "<tr id = sch_main>";
					echo 		"<td id = sch_para>".($i+1)."</td>";
					echo		"<td id = sch_cont><p class = pred>Предмет</p>";
					echo		"<p class = prep>Преподаватель</p></td>";
					echo 		"<td id = sch_type>тип</td>";
					echo 		"<td id = sch_audt>ауд</td>";
					echo "</tr>";
				}
			}
		?>
		</table>
	</div>
</div>
