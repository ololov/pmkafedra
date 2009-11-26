<?php
require_once('libdb.php');
?>
<div id = "vmenu">Дополнительное меню</div>
<div id = "main">
<form action="?page=pmlib&uploadfile=1" method="post">
<p><input type="text" name="<?php echo book_title; ?>" />
<p><input type="text" name="<?php echo book_author; ?>" />
<p><input type="text" name="<?php echo book_volume; ?>" />
<p><input type="text" name="<?php echo book_publish; ?>" />
<p><input type="text" name="<?php echo book_year; ?>" />
<p><input type="text" name="<?php echo book_isbn; ?>" />
<p><input type="text" name="<?php echo book_pages; ?>" />
<p><input type="file" name="<?php echo book_file; ?>" />
<p><textarea><input name="<?php echo book_desc; ?>" rows=20 cols=60></textarea>
</form>
</div>

