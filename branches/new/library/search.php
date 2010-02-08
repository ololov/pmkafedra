<?php
require_once('include/auth.php');
init_logins();

require_once('include/site.php');
require_once('./lib/site.php');
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
<p class="tit">Поиск книги</p>
<form id = "form_search" method = "GET" action = "<?php printf("%s/list.php", lib_url);?>">
	<input type="hidden" name="page" value="pmlib" />
	<input type="hidden" name="view" value="list" />
	<fieldset>
		<p>Поиск по автору книги: <input type="text" name="s_author" class = "input_text"></p>
		<p>Поиск по названию книги: <input type="text" name="s_book" class = "input_text"></p>
		<p>Поиск по разделам: <input type="text" name="s_dep" class = "input_text"></p>
	</fieldset>
	<p><input type="submit" value="Поиск" class="buttonSubmit" /></p>
</form>
</div>
</body>
</html>
