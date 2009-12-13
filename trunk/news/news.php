<div id = "main">
	<p class = "tit">Добавление новости</p>
	<?php include_once("add_new.php"); ?>
	<form method = post id = "add_news">
	<fieldset id = "type_news">
		<p>Выберите тип новости:
		<select name = "types">
			<option value = "sch">Изменения в расписании</option>
			<option value = "lib">Новости библиотеки</option>
			<option value = "den">Новости деканата</option>
			<option value = "adt">Объявление</option>
			<option value = "oth">Другое</option>
		</select> </p>
	</fieldset>
	
	<fieldset id = "com_news">
		<p>Тема: <input name="headline" type="text" class = "theme"> </p>
		<p>Новость: <textarea name="message" cols = 50 rows = 5></textarea> </p>
	</fieldset>

	<input type=submit value="Добавить" class = "buttonSubmit" name = "add">
	</form>
</div>
