<?php
	if (isset($_GET['id_pr'])) {
		include_once('info/prepod.php');
		exit;
	}
?>

<div id = "separator"></div>
<?php include_once("info/sidebar.php"); ?>
<div id = "main">
	<p class = "tit">Преподаватели и сотрудники кафедры</p>
	<table>
		<tr class = "odd">
     			<td><a href = "?page=staff&id_pr=1">Кузнецов Валерий Леонидович</a></td>
			<td>профессор, заведующий кафедрой</td>
			<td>kuznetsov@mstuca.ru</td>
		</tr>
		<tr>
     			<td><a href = "?page=staff&id_pr=2">Егорова Алла Альбертовна</a></td>
			<td>профессор</td>
			<td>ego_alla@mail.ru</td>
		</tr>
     		<tr class = "odd">	
			<td><a href = "?page=staff&id_pr=3">Ерзакова Нина Александровна</a></td>
			<td>профессор</td>
			<td>...</td>
		</tr>
		<tr>
     			<td><a href = "?page=staff&id_pr=4">Калмыков Георгий Иванович</a></td>
			<td>профессор</td>
			<td>...</td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=staff&id_pr=5">Котиков Вячеслав Иванович</a></td>
			<td>профессор</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "?page=staff&id_pr=6">Аль-Натор Мухаммед Субхи</a></td>
			<td>доцент</td>
			<td>malnator@yandex.ru</td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=staff&id_pr=7">Аль-Натор Софья Владимировна</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "?page=staff&id_pr=8">Ивенин Игорь Борисович</a></td>
			<td>доцент</td>
			<td>ibi.new@mail.com</td>
		</tr>
		<tr class = "odd">
			<td><a href = "">Касимов Юрий Федорович</a></td>
			<td>доцент, заведуюший лабораторией</td>
			<td>...</td>
		</tr>
		<tr>
			<td><a href = "?page=staff&id_pr=9">Коновалов Владимир Михайлович</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=staff&id_pr=10">Коротков Александр Сергеевич</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "?page=staff&id_pr=11">Лоссиевская Татьяна Владимировна</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr class = "odd">	
			<td><a href = "?page=staff&id_pr=12">Пичугин Андрей Анатольевич</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr>	
			<td><a href = "?page=staff&id_pr=13">Столяров Андрей Викторович</a></td>
			<td>доцент</td>
			<td>...</td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=staff&id_pr=14">Ивенина Елена Михайловна</a></td>
			<td>старший преподаватель</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "?page=staff&id_pr=17">Андреева Татьяна Ильинична</a></td>
			<td>старший преподаватель</td>
			<td>...</td>
		</tr>
     		<tr class = "odd">
			<td><a href = "?page=staff&id_pr=15">Петрова Людмила Владимировна</a></td>
			<td>старший преподаватель</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "?page=staff&id_pr=16">Филонов Павел Владимирович</a></td>
			<td>аспирант, преподаватель</td>
			<td>filonovpv@gmail.com</td>
		</tr>
		<tr class = "odd">
			<td><a href = "">Наделяева Людмила Михайловна</a></td>
			<td>методист</td>
			<td>...</td>
		</tr>
     		<tr>
			<td><a href = "">Никифорова Н.А.</a></td>
			<td>ведущий документовед</td>
			<td>...</td>
		<tr>
	</table>
</div>
