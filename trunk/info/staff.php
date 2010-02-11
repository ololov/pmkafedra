<?php
include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

$sql = "SELECT worker_login, worker_name, worker_seat, worker_contact FROM workers_tb ORDER BY worker_name;";
$res = pg_query($link, $sql);
if (!$res)
	include_once('include/html_db_error.php');

?>
<!DOCTYPE html>
<html>
<?php
include_once('include/site.php');
include_once('./lib/site.php');

print_head("Кафедра");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<p class = "tit">Преподаватели и сотрудники кафедры</p>
<table>
<?php
if (pg_num_rows($res) == 0) {
	write_user_message("Ресурс не добавлен");
} else {
	$classes = array("class = \"odd\"", "");
	$i = 0;
	$fmt = "<tr %s><td><a href = \"%s\">%s</a></td><td>%s</td><td>%s</td></tr>";
	while ($row = pg_fetch_assoc($res)) {
		printf($fmt, $classes[$i % 2],
			info_url . htmlspecialchars("/worker.php?wid=") . $row['worker_login'],
			$row['worker_name'], $row['worker_seat'], $row['worker_contact']);
		$i++;
	}
}
?>
</table>
</div>
</body>
</html>
