<p class="tit">Добавить книгу</p>
<form action="?page=pmlib&amp;uploadfile=1" method="post" enctype="multipart/form-data">
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
	<p><input type="submit" name="Загрузить" value="Загрузить" class="buttonSubmit" /></p>
</form>


