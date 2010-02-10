<?php
include_once('include/auth.php');
init_logins();

include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_errors.php');

$group = "disc_id, disc_name, disc_lessons, disc_practices, disc_labs, disc_courseovik";
/*
$alias_id = "wids";
$agg_id = "array_agg(worker_id)";
$alias_name = "names";
$agg_name = "array_agg(worker_name)";
$subquery = "SELECT course_number FROM dk_tb WHERE dk_tb.disc_id = wdfull_tb.disc_id ORDER BY course_number";

$query = "SELECT $group, $agg_id AS $alias_id, $agg_name AS $alias_name, ARRAY($subquery) " .
       "FROM wdfull_tb GROUP BY $group ORDER BY disc_name;";
 */
$subquery = "SELECT course_number FROM dk_tb WHERE dk_tb.disc_id = disc_tb.disc_id ORDER BY course_number";
$query = "SELECT $group, ARRAY($subquery) " .
       "FROM disc_tb ORDER BY disc_name;";

$res = pg_query($link, $query);
if (!$res)
	include_once('include/html_db_erros.php');

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
<div>
<table>
<p class = "tit">Дисциплины</p>
<tr class = "odd" align = "center">
<td>Название</td><td>лекций</td><td>практическиe занятия</td>
<td>лабораторные занятия</td><td>Курсовая работа</td>
</tr>
<?php

$i = 0;
$classes = array("", "class = \"odd\"");
while ($row = pg_fetch_assoc($res)) {
	printf("<tr %s align = \"center\"><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
		$classes[$i % 2], $row['disc_name'], $row['disc_lessons'],
		$row['disc_practices'], $row['disc_labs'], $row['disc_courseovik']);
	++$i;
}
?>
</table>
</div>
</div>
</body>
</html>
