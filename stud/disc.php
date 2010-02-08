<?php
include_once('include/auth.php');
init_logins();

include_once('include/lib.php');
/*
$link = db_connect();
if (!$link)
	include_once('include/html_db_errors.php');
 */
?>
<!DOCTYPE html>
<html>
<?php
include_once('include/site.php');
include_once('./lib/site.php');

print_head("Главная страница");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<p>Пока не реализовано
</div>
</body>
</html>
