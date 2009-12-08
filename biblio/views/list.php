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

function getq_from_search($from, $limit)
{
	$fmt = <<<EOF
SELECT
	book_id, book_name,
	array_agg(author_id) AS author_ids,
	array_agg(author_name) AS author_names,
	(SELECT book_path FROM books_tb WHERE books_tb.book_id = ab_tb.book_id) AS book_path
FROM
	ab_tb
WHERE
	%s %s %s %s %s
GROUP BY
	book_id, book_name
ORDER BY
	book_name
LIMIT %d OFFSET %d;
EOF;
	if ($_REQUEST['search_name'] != '')
		$book_name = sprintf("book_name LIKE '%%%s%%'", $_REQUEST['search_name']);
	if ($_REQUEST['search_author'] != '')
		$author_name = sprintf("author_name LIKE '%%%s%%'", $_REQUEST['search_author']);
	if ($_REQUEST['search_dep'] != '')
		$dep = sprintf("book_dep LIKE '%%%s%%'", $_REQUEST['search_dep']);

	if ($book_name != '')
		$op1 = 'OR';
	if ($dep != '')
		$op2 = 'OR';

	return sprintf($fmt, $book_name, $op1,
		$author_name, $op2, $dep,
		$limit, $from);

}

function getq_count_from_search()
{
	$fmt = <<<EOF
SELECT
	COUNT(book_id)
FROM
	ab_tb
WHERE
	%s %s %s %s %s;
EOF;
	if ($_REQUEST['search_name'] != '')
		$book_name = sprintf("book_name LIKE '%%%s%%'", $_REQUEST['search_name']);
	if ($_REQUEST['search_author'] != '')
		$author_name = sprintf("author_name LIKE '%%%s%%'", $_REQUEST['search_author']);
	if ($_REQUEST['search_dep'] != '')
		$dep = sprintf("book_dep LIKE '%%%s%%'", $_REQUEST['search_dep']);

	if ($book_name != '')
		$op1 = 'OR';
	if ($dep != '')
		$op2 = 'OR';

	return sprintf($fmt, $book_name, $op1, $author_name, $op2, $dep);
}

function getq_list($from, $count)
{
	$query = <<<EOF
SELECT
	book_id,
	book_name,
	array_agg(author_id) AS author_ids,
	array_agg(author_name) AS author_names,
	(SELECT book_path FROM books_tb WHERE books_tb.book_id = ab_tb.book_id) AS book_path
FROM
	ab_tb
GROUP BY
	book_id, book_name
ORDER BY
	book_name
EOF;
	return "$query LIMIT $count OFFSET $from;";
}

function getq_count()
{
	return "SELECT COUNT(book_id) FROM books_tb;";
}

function getq_list_by_author($from, $count, $aid)
{
	$query = <<<EOF
SELECT
	book_id,
	book_name,
	array_agg(author_id) AS author_ids,
	array_agg(author_name) AS author_names,
	(SELECT book_path FROM books_tb WHERE books_tb.book_id = ab_tb.book_id) AS book_path
FROM
	ab_tb
GROUP BY
	book_id, book_name
HAVING
	%d = ANY(array_agg(author_id))
ORDER BY
	book_name
LIMIT %s OFFSET %d;
EOF;
	return sprintf($query, $aid, $count, $from);
}

function getq_count_by_author($aid)
{
	return sprintf("SELECT COUNT(book_id) FROM ab_tb WHERE author_id = %d;",
			$aid);
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
define('capacity', '25');

$link = db_connect() or die(pg_last_error());

$from = 0;
$pages = 1;

if (isset($_GET['lists']) && is_numeric($pages = $_GET['lists'])) {
	if ($pages < 1)
		$pages = 1;
	$from = ((int)$pages - 1) * capacity;
}

if (isset($_REQUEST['search_name']) && ($_REQUEST['search_name'] != '') ||
    isset($_REQUEST['search_author']) && ($_REQUEST['search_author'] != '') ||
    isset($_REQUEST['search_dep']) && ($_REQUEST['search_dep'] != '')) {
	$query = getq_from_search($from, capacity);
	$count_query = getq_count_from_search();
	$param2=sprintf("&search_name=%s&search_author=%s&search_dep=%s",
			$_REQUEST['search_name'],
			$_REQUEST['search_author'],
			$_REQUEST['search_dep']);
} else if (isset($_GET['a_id']) && is_numeric($aid = $_GET['a_id'])) {
	$query = getq_list_by_author($from, capacity, $aid);
	$param2 = "&a_id=$aid";
	$count_query = getq_count_by_author($aid);
} else {
	$query = getq_list($from, capacity);
	$count_query = getq_count();
}
/* */
$str = "http://".trim($_SERVER['HTTP_HOST'], '/').$_SERVER['SCRIPT_NAME'];

$url = htmlspecialchars("$str?page=pmlib&view=list$param2");

/*
 * Узнаем сколько всего книг
 */
$res = pg_query($link, $count_query) or die(pg_last_error());
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
