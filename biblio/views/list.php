<?php
require_once('./biblio/dbconst.php');
require_once('./biblio/libview.php');
require_once('./include/lib.php');

$limit = 25;
$limit_page = 10;

/*
 * Local funcitons
 */
function throw_exception()
{
	throw new Exception(pg_last_error());
}

function get_maxpage($res)
{
	global $limit;

	$all = 0;
	while ($row = pg_fetch_array($res))
		$all += $row[0];
	
	return $all / $limit + (($all % $limit) ? (1) : (0));
}

function get_page_list($curr, $max)
{
	global $limit, $limit_page;
	$curr_url = $_SERVER['REQUEST_URI'];
	$curr_url = mb_eregi_replace("\&?pg=[0-9]+", '', $curr_url);

	/* check left bound */
	$side = (int)($limit_page / 2);

	$left = $curr - $side + 1;
	$right = $curr + $side;
	$diff = $right - $left;

	if ($max >= $limit_page) {
		if ($left < 1) {
			$left = 1;
			$right = $limit_page;
		} else if ($right > $max) {
			$right = $max;
			$left = $max - $limit_page + 1;
		}
	} else {
		$left = 1;
		$right = $max;
	}

	$ldots = ($left == 1) ? ('') : ('...');
	$rdots = ($right == $max) ? ('') : ('...');

	$res = "<div id=\"pagelist\">$ldots";
	for ($i = $left; $i <= $right; ++$i)
		if ($i == $curr) {
			$res .= "$i ";
		} else {
			$res .= tag_href($curr_url . htmlspecialchars("&pg=$i"), "$i ");
		};
	$res .= " $rdots</div>";
	return $res;
}

/*
 * Search parameters
 */
$authors = '';
$book_name = '';
$book_dep = '';

/*
 * SQL parameters;
 */
$groups = 'tr.book_id, book_name, book_path';
$id_agg = 'array_agg(tr.author_id)';
$author_agg = 'array_agg(author_name)';
$alias_id_agg = 'authors_ids';
$alias_author_agg = 'authors_names';

/*
 * Connecting to database
 */
$link = db_connect_ex();

/*
 * First parse search parameters
 */
if (isset($_REQUEST['s_author'])) {
	$pauthors = pg_escape_string($link, $_REQUEST['s_author']);
	$authors = "array_to_string($author_agg, '') LIKE '%$pauthors%'";
	$c_authors = "ta.author_name LIKE '%$pauthors%'";
} else if (isset($_REQUEST['author_id']) && is_numeric($_REQUEST['author_id'])) {
	/* user could click on author name, so we should print all his books */
	$authors = sprintf("%d = ANY($id_agg)", $_REQUEST['author_id']);
	$c_authors = sprintf("ta.author_id = %d", $_REQUEST['author_id']);
}

if (isset($_REQUEST['s_book'])) {
	$book_name = pg_escape_string($link, $_REQUEST['s_book']);
	$book_name = "book_name LIKE '%$book_name%'";
}

/*
 * FIXME:
 * 	Поиск по разделам пока не реализован
 */

if ($authors != '') {
	$authors = " HAVING $authors ";
	$c_authors = " tb.book_id IN (SELECT tr.book_id FROM ab_tb AS tr " .
		     "INNER JOIN authors_tb AS ta ON(tr.author_id = ta.author_id) " .
		     "WHERE tr.book_id = tb.book_id AND $c_authors) ";
}
if ($book_name != '') {
	$book_name = " WHERE $book_name ";
}

$and = (($book_name != '' && $c_authors != '') ? ('AND') : (''));
$c_where = "$book_name $and $c_authors";

$cquery= "SELECT COUNT(*) FROM books_tb AS tb $c_where;";
/*
 * Get max and current page.
 */
$cres = db_query_ex($link, $cquery);

$maxpages = get_maxpage($cres);
if (isset($_GET['pg']))
	$page = (int)$_GET['pg'];
else
	$page = 1;

if ($page > $maxpages)
	/* Вместо демонстрации ошибки выведем последнюю страницу */
	$page = $maxpages;

$offset = ($page - 1) * $limit;
/* offset может быть < 0 */
$offset = ($offset < 0) ? (0) : ($offset);

//echo "$page<br>$maxpages<br>$offset";
/* Основной запрос */
$query = "SELECT $groups, $id_agg AS $alias_id_agg, $author_agg AS $alias_author_agg " .
	 "FROM ab_tb AS tr INNER JOIN books_tb AS tb ON(tr.book_id = tb.book_id) " .
	 "INNER JOIN authors_tb AS ta ON(tr.author_id = ta.author_id) " .
	 "$book_name GROUP BY $groups $authors LIMIT $limit OFFSET $offset;";


$res = db_query_ex($link, $query);

if (pg_num_rows($res) == 0) {
	write_user_message("Такой книги нету");
} else {
	/*
	 * Printing result
	 */
	$i = 0;
	$classes = array('odd', '');
	
	$pg_list = get_page_list($page, $maxpages);

	print($pg_list);
	print("<div><table>");
	while ($row = pg_fetch_assoc($res)) {
		$author_list = trim($row[$alias_author_agg], '{} ');
		$author_list = str_replace('"', '', $author_list);
		$author_list = explode(',', $author_list);
	
		$author_id_list = trim($row[$alias_id_agg], '{} ');
		$author_id_list = explode(',', $author_id_list);
		/*
		 * Prepare to print
		 */
		$fields = array();
		$fields[] = make_href($biblio_url . htmlspecialchars("&author_id="),
				      $author_list, $author_id_list);
		$fields[] = tag_href($biblio_url . htmlspecialchars("&view=desc&book_id=" . $row['book_id']),
		       		      $row['book_name']);
		$fields[] = tag_href($row['book_path'], "МОЁ!!!");
	
		print_table_row($classes[$i++ % 2], $fields);
	}
	print("</table></div>");
	print($pg_list);
}

?>
