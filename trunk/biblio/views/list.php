<?php
require_once('biblio/dbconst.php');
require_once('biblio/libview.php');

/*
 * Local functions
 */

function row_template()
{
	return <<<EOF
<tr class=%s>
<td>%s</td><td>%s</td><td><a href="%s">Скачать</a></td>
</tr>
EOF;
}

$link = db_connect() or die(pg_last_error());

if (isset($_GET['a_id']) && is_numeric($aid = $_GET['a_id']))
	$query = get_query_list_by_author(0, 'ALL', $aid);
else
	$query = get_query_list(0, 'ALL');

$res = pg_query($link, $query) or die(pg_last_error());
?>

<p class="tit">Все книги</p>
<table>
<?php
$i = 0;
$class = array(books_table_row, '');
$fmt = row_template();

while ($row = pg_fetch_assoc($res)) {
	$alist = explode(',', clean_string($row[author_names]));
	$ilist = explode(',', clean_string($row[author_ids]));

	printf($fmt, $class[($i++) % 2],
		make_href(list_path, $alist, $ilist),
		tag_href(desc_path . $row[book_id], $row[book_name]),
		$row[book_path]);
}
?>
</table>
