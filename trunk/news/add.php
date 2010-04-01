<?php
	require_once('include/auth.php');
	init_logins();
	redirect_user(A_ADD_NEWS, lib_url);

	include_once('include/site.php');
	include_once('./lib/site.php');
?>
<!DOCTYPE html>
<html>
	<?php
		print_head("Новости");
	?>
<body>
	<?php
		print_header();
		print_sidebar();
	?>
	<div id = "<?php echo css_content_div; ?>">
		<p class = "tit">Добавление новости</p>
		<form action="add_new.php" method = "POST" id = "add_news">
			<fieldset id = "type_news">
			<p>Выберите тип новости:
			<select name = "types">
				<option>Изменения в расписании</option>
				<option>Новости библиотеки</option>
				<option>Новости деканата</option>
				<option>Другое</option>
			</select> 
			</p>
			</fieldset>
	
			<fieldset id = "com_news">
			<p>Тема: <input name="headline" type="text" class = "theme"> </p>
			<p>Новость: <textarea name="message" cols = 50 rows = 5></textarea> </p>
			</fieldset>

			<input type=submit value="Добавить" class = "buttonSubmit" name = "add">
		</form>
	</div>
</body>
</html>
