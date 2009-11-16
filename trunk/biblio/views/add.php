<?php
require_once('biblio/mylib.php');
?>
<p class="tit">Добавить книгу</p>
<form action="?page=pmlib&uploadfile=1" method="post">
	<fieldset>
		<legend>Информация о книге</legend>
		<p>Название книги<br>
	    	<input type="text" name="<?php echo book_title; ?>" />
		<p>Авторы книги<br> 
		<input type="text" name="<?php echo book_author; ?>" />
	</fieldset>
	<fieldset>
		<legend>Дополнительная информация о книге</legend>
		<p>Номер тома книги<br>
		<input type="text" name="<?php echo book_volume; ?>" />
		<p>Издательство<br>
		<input type="text" name="<?php echo book_publish; ?>" />
		<p>Год издания<br>
		<input type="text" name="<?php echo book_year; ?>" />
		<p>ISBN<br>
		<input type="text" name="<?php echo book_isbn; ?>" />
		<p>Количество страниц<br>
		<input type="text" name="<?php echo book_pages; ?>" />
	</fieldset>
	<fieldset>
		<legend>Загрузить книгу</legend>
		<input type="file" name="<?php echo book_file; ?>" />
	</fieldset>
	<fieldset>
	<p><b>Описание книги:</b></p>
	<p><textarea name="<?php echo book_desc;?>" cols=50 rows=10></textarea></p>
	</fieldset>
	<p><input type="submit" name="Загрузить" /></p>
</form>


