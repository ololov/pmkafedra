<?php 
	include_once("students/s_sidebar.php"); 
	$script = array('d1' => 'students/d1.php', 'd2' => 'students/d2.php','d3' => 'students/d3.php',
			'd4' => 'students/d4.php', 'd5' => 'students/d5.php','d6' => 'students/d6.php',
			'd7' => 'students/d7.php', 'd8' => 'students/d8.php','d9' => 'students/d9.php',
			'd10'=> 'students/d10.php','d11'=> 'students/d11.php');
	$pages = $_GET['pages'];
	if (isset($pages) && array_key_exists($pages, $script)){
		include_once($script[$pages]);
		exit;
	}
	else
		include_once("students/discipl.php");
?>

<div id = "main">
	<p class = "tit">Дисциплины государственных экзаменов</p>
	<table class="st">
		<tr class = "odd">
			<td><a href = "?page=stud&amp;pages=d1">Алгебра и аналитическая геометрия</a></td>
		</tr>
		<tr>
     	    		<td><a href = "?page=stud&amp;pages=d2">Математический анализ</a></td>
		</tr>
     		<tr class = "odd">	
			<td><a href = "?page=stud&amp;pages=d3">Дискретная математика</a></td>
		</tr>
		<tr>
     			<td><a href = "?page=stud&amp;pages=d4">Теория вероятности и математическая статистика</a></td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=stud&amp;pages=d5">Алгоритмические языки и программирование</a></td>
		</tr>
     		<tr>
			<td><a href = "?page=stud&amp;pages=d6">Информационные структуры и методы обработки информации</a></td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=stud&amp;pages=d7">Компьютерная графика</a></td>
		</tr>
     		<tr>
			<td><a href = "?page=stud&amp;pages=d8">Программные системы машинной графики</a></td>
		</tr>
		<tr class = "odd">
			<td><a href = "?page=stud&amp;pages=d9">Информационные системы</a></td>
		</tr>
		<tr>
			<td><a href = "?page=stud&amp;pages=d10">Системное и прикладное программное обеспечение</a></td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=stud&amp;pages=d11">Базы данных</a></td>
		</tr>
	</table>
</div>
