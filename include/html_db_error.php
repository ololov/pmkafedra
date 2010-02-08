<?php
include_once('include/site.php');
?>
<!DOCTYPE html>
<html>
<?php
print_head("Ошибка");
?>
<body>
<?php
print_header();
?>
<div id = "vmenu">
</div>
<div id = "<?php echo css_content_div; ?>">
<div id = "umsg">
Ошибка на сервера базы данных. Попробуйте позже
</div>
</div>
</div>
</body>
</html>
<?php exit; ?>
