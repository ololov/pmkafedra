<?php
include_once('include/auth.php');
init_logins();

include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

$wheres = array("EXTRACT(year FROM book_posted) = EXTRACT(year FROM NOW())",
		"EXTRACT(month FROM book_posted) = EXTRACT(month FROM NOW())",
		"EXTRACT(week FROM book_posted) = EXTRACT(week FROM NOW())",
		"EXTRACT(day FROM book_posted) = EXTRACT(day FROM NOW())",
		""
);
$res = array();
$sql = "SELECT COUNT(*) FROM books_tb WHERE " . $wheres[0];
for ($i = 1; $i < count($wheres); ++$i) {
	$tres = pg_query($link, $sql);
	if (!$tres)
		include_once('include/html_db_error.php');
	$res[] = pg_fetch_array($tres);
	$sql .= " AND " . $wheres[$i];
}

?>
<!DOCTYPE html>
<html>
<?php
include_once('include/site.php');
include_once('./lib/site.php');

print_head("Библиотека");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<p class = "tit">Библиотека кафедры "Прикладной матемитики"</p>
<div>
<table align = "center">
<caption><h3>Добавлено книг</h3></caption>
<?php
$strs = array("В этом году:", "В этом месяце:", "На этой недели", "Сегодня");

for ($i = count($res) - 1; $i > -1; --$i) {
	$row = $res[$i];
	printf("<tr><td>%s</td><td align = center>%s</td></tr>",
		$strs[$i], $row[0]);
}
?>
</table>
</div>
</div>
</body>
</html>
