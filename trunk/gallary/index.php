<?php
include_once('include/auth.php');
init_logins();

include_once('include/site.php');
include_once('./lib/site.php');
?>
<!DOCTYPE html>
<html>
<?php
print_head("Галерея");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>"></div>
</body>
</html>
