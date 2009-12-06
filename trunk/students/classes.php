<div id = "main">
	<?php
		include_once("students/classlib.php");
		$doc = new domDocument(); $doc->load("students/discipline.xml");
		$class = view($doc);
	?>
	<table>
		<tr>
			<td>Предмет</td>
			<td>Преподаватель</td>
			<td>Курс</td>
			<td>Форма контроля</td>
		</tr>
		<?php 
			$i = 1;
			foreach($class as $v) {
				if (($i % 2) == 0) echo "<tr>";
				else		   echo "<tr class = odd>";
				echo 	"<td>".$v['titl']."</td>";
				echo 	"<td>".$v['prep']."</td>";
				echo 	"<td>".$v['kurs']."</td>";
				echo	"<td>".$v['cntr']."</td>";
				echo "</tr>";
				$i++;
			}
		?>
	</table>
</div>
