<?php 
	$script = array('d1' => 'd1.php', 'd2' => 'd2.php','d3' => 'd3.php',
			'd4' => 'd4.php', 'd5' => 'd5.php','d6' => 'd6.php',
			'd7' => 'd7.php', 'd8' => 'd8.php','d9' => 'd9.php',
			'd10'=> 'd10.php','d11'=> 'd11.php');
	$pages = $_GET['pages'];
	if (isset($pages) && array_key_exists($pages, $script)){
		include_once($script[$pages]);
		exit;
	}
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
