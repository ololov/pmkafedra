<?php
include_once('include/auth.php');
init_logins();

include_once('include/site.php');
?>
<!DOCTYPE html>
<html>
<?php
print_head("Главная страница");
?>
<body>
<?php
print_header();
?>
<div id = "vmenu">
<?php
print_login_form();
?>
</div>
<div id = "<?php echo css_content_div; ?>">
<div id = "umsg">
Неправильный логин и/или пароль.
</div>
</div>
</body>
</html>
<?php exit; ?>
