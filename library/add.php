<?php
require_once('include/auth.php');
init_logins();
redirect_user(A_ADD_BOOK, lib_url);

include_once('include/site.php');
include_once('./lib/site.php');
?>
<!DOCTYPE html>
<html>
<?php
print_head("Библиотека");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<p class="tit">Добавить книгу</p>
<form action="<?php printf("%s/upload.php", lib_url); ?>" method="POST" enctype = "multipart/form-data">
	<div id = "book_info_main">
	<fieldset>
		<legend>Информация о книге</legend>
		<p>Название книги<br>
	    	<input type="text" name="book_title" />
		<p>Авторы книги<br> 
		<input type="text" name="book_author" />
		<p>Описание книги<br>
		<textarea name="book_desc" cols=50 rows=10></textarea>
		<p>Загрузить книгу<br>
		<input type="file" name="book_file" />
	</fieldset>
	</div>
	<div id = "book_info_dop">
	<fieldset>
		<legend>Дополнительная информация о книге</legend>
		<p>Номер тома книги<br>
		<input type="text" name="book_volume" />
		<p>Издательство<br>
		<input type="text" name="book_publish" />
		<p>Год издания<br>
		<input type="text" name="book_year" />
		<p>ISBN<br>
		<input type="text" name="book_isbn" />
		<p>Раздел<br>
		<input type="text" name="book_dep" />
	</fieldset>
	</div>
	<p><input type="submit" name="upload" value="Загрузить" class="buttonSubmit" /></p>
</form>
</div>
</body>
</html>
