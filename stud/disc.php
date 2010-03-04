<?php
include_once('include/auth.php');
init_logins();

include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

$group = "disc_name, disc_lessons, disc_practices, disc_labs, disc_courseovik";
/*
$alias_id = "wids";
$agg_id = "array_agg(worker_id)";
$alias_name = "names";
$agg_name = "array_agg(worker_name)";
$subquery = "SELECT course_number FROM dk_tb WHERE dk_tb.disc_id = wdfull_tb.disc_id ORDER BY course_number";

$query = "SELECT $group, $agg_id AS $alias_id, $agg_name AS $alias_name, ARRAY($subquery) " .
       "FROM wdfull_tb GROUP BY $group ORDER BY disc_name;";
 */
$alias_course = "courses";
$subquery = "SELECT course_number FROM dk_tb WHERE dk_tb.disc_name = disc_tb.disc_name ORDER BY course_number";
$query = "SELECT $group, ARRAY($subquery) AS $alias_course " .
       "FROM disc_tb WHERE isprof ORDER BY disc_name;";

$res = pg_query($link, $query);
if (!$res)
	include_once('include/html_db_error.php');

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
<tr class = "head_odd" align = "center">
	<td>Название</td>
	<td>Курс</td>
	<td>Курсовая работа</td>
</tr>
<?php

$i = 0;
$classes = array("", "class = \"odd\"");
while ($row = pg_fetch_assoc($res)) {
	printf("<tr %s><td>%s</td><td>%s</td><td>%s</td></tr>",
		$classes[$i % 2], $row['disc_name'], clean_string($row[$alias_course]),
		$row['disc_courseovik']);
	++$i;
}
?>
</table>
</div>
</div>
</body>
</html>
