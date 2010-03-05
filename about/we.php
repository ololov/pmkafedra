<!DOCTYPE html>
<html>
	<?php
		include_once('include/site.php');
		include_once('./lib/site.php');

		print_head("О сайте");
	?>
	<body>
		<?php
			print_header();
			print_sidebar();
		?>
		
		<div id = "<?php echo css_content_div; ?>">
		<p class = "tit">Разработчики сайта</p>
		<div>
			<table id = "about">
				<tr>	<td class = "t_photo"><img  src="photo/5.png" class = "worker_photo"></td>
					<td><p class = "subtit">Бояркин Сергей</p>
						Проделал огромную работа, переведя excel-файлы
						в расписание, которое теперь красуется на нашем сайте. 
						Главный разработчик Галереи. Именно Сергей проталкивает в нашей консервативной компании модные 
						технологии типа AJAX.
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/6.jpg" class = "worker_photo"></td>
					<td><p class = "subtit">Исаев Сергей</p>
						Главный разработчик Форума. Один из самых опытных людей в нашей команде. Неоднократно
						помогал и делом и разумным советом. Ждем окончания эпопеи с форумом.
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/4.jpg" class = "worker_photo"></td>
					<td><p class = "subtit">Исубилов Руслан</p>
						Новый человек в нашей команде. Руслан работает над проблемой
						вывода сайта в интернет. Ждем от него свежих и нестандартных решений.
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/7.jpeg" class = "worker_photo"></td>
					<td><p class = "subtit">Ким Максим</p>
						"Двигатель" нашего проекта. Главный разработчик библиотеки. Кроме того, внес
						немало изменений в базу данных и другие разделы. Максим стремится максимально 
						оптимизировать работу сайта и ему это безусловно удается.
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/3.png" class = "worker_photo"></td>
					<td><p class = "subtit">Куликова Юлия</p>
						CSS-верстка сайта и решение организационных вопросов.
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/1.png" class = "worker_photo"></td>
					<td><p class = "subtit">Потемкина Светлана</p>
						Светлане принадлежит идея создать Галерею и тем
						самым немного разнообразить жизнь нашего сайта. Также она
						принимала участие в создании раздела "Новости".
					</td>
				</tr>
				<tr>	<td class = "t_photo"><img  src="photo/2.png" class = "worker_photo"></td>
					<td><p class = "subtit">Радькова Мария</p>
						Оказала неоценимую помощь в подборе материалов для сайта, чем 
						существенно упростила жизнь других участников проекта.
						Именно Марию мы благодарим за создание эмблемы кафедры.
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
