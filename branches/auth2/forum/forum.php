<?php
	if (isset($_GET['id'])) {
		include_once('teams.php');
		exit;
	}
?>
<div id = forum_main>
	<table id = "forum">
		<tr class = "head_forum">
			<td class = "first">Форум</td>
			<td>Тем</td>
			<td>Ответов</td>
			<td class = "last">Последний ответ</td>
		</tr>
		<tr>
			<td>
				<img src = "images/forum1.png" class = "img_forum">
				<a href = "?page=forum&id=1" class = "team">Раздел 1</a>
				<p class = "note">Пояснения к разделу 1</p>
			</td>
			<td>12</td>
			<td>698</td>
			<td>12.11.09 by Ifgr</td>
		</tr>
		<tr>
			<td>
				<img src = "images/forum2.png" class = "img_forum">
				<a href = "" class = "team">Раздел 2</a>
				<p class = "note">Пояснения к разделу 2</p>
			</td>
			<td>4</td>
			<td>8</td>
			<td>11.10.09 by DEF</td>
		</tr>
		<tr>
			<td>
				<img src = "images/forum3.png" class = "img_forum">
				<a href = "" class = "team">Раздел 3</a>
				<p class = "note">Пояснения к разделу 3</p>
			</td>
			<td>114</td>
			<td>1113</td>
			<td>23.11.09 by Alex</td>
		</tr>
	</table>
</div>
