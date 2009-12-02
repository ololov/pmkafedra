<?php
require_once('./biblio/dbconst.php');
require_once('./biblio/libview.php');


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


function make_lists_href($curr, $all)
{
	global $url;
	$count = (int)abs($all - 1) / capacity + 1;

	for ($i = 1; $i <= $count; ++$i)
		if ($i != $curr)
			$ret .= "<a href=\"$url&amp;lists=$i\">$i</a> ";
		else
			$ret .= "$i ";

	return $ret;
}

/* Кол-во книг выводимых на одной странице */
define('capacity', '10');

$link = db_connect() or die(pg_last_error());

$from = 0;
$pages = 1;

if (isset($_GET['lists']) && is_numeric($pages = $_GET['lists'])) {
	if ($pages < 1)
		$pages = 1;
	$from = ((int)$pages - 1) * capacity;
}

if (isset($_GET['a_id']) && is_numeric($aid = $_GET['a_id'])) {
	$query = get_query_list_by_author($from, capacity, $aid);
	$param2 = "&amp;a_id=$aid";
} else
	$query = get_query_list($from, capacity);

/* */
$str = "http://".trim($_SERVER['HTTP_HOST'], '/').$_SERVER['SCRIPT_NAME'];

$url = "$str?page=pmlib&amp;view=list$param2";

/*
 * Узнаем сколько всего книг
 */
$res = pg_query($link, "SELECT COUNT(*) FROM books_tb;") or die(pg_last_error());
$tmp = pg_fetch_array($res);
$rows = $tmp[0];

/*
 * Посылка основного запроса
 */
$res = pg_query($link, $query) or die(pg_last_error());

if (pg_num_rows($res) == 0)
	die("Нет таких книг");
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
<div>
<p align=center>
<?php
echo make_lists_href($pages, $rows);
?>
</p>
</div>
