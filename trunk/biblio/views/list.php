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
 * Getting search parameters
 */

function check_parameters($pnames, $container)
{
	for ($i = 0; $i < count($pnames); ++$i)
		if (!isset($container[$pnames[$i]]))
			return FALSE;
	return TRUE;
}

/*
 * Connect to database
 */
$link = db_connect_ex();

/*
 * Check search parameters first!
 */
$sql_req = array("s_book" => "SELECT book_id FROM books_tb WHERE book_name ILIKE '%%%s%%'",
		 "s_author" => "SELECT book_id FROM abfull_tb WHERE author_name ILIKE '%%%s%%'",
		 "s_dep" => "SELECT book_id FROM dbfull_tb WHERE dep_name ILIKE '%%%s%%'");
$params = array("s_book", "s_author", "s_dep");
$sql_fin = array();
if (check_parameters($params, $_GET)) {
	for ($i = 0; $i < count($params); ++$i) {
		$curr = trim($_GET[$params[$i]]);
		if ($curr != '') {
			$curr = pg_escape_string($link, $curr);
			$sql_fin[] = sprintf($sql_req[$params[$i]], $curr);
		}
	}
} else if (isset($_GET['author_id'])) {
	$val = $_GET['author_id'];
	if (is_numeric($val) && (int)$val = $val)
		$sql_fin[] = sprintf("SELECT book_id FROM ab_tb WHERE author_id = %d", $val);
}

/*
 * Prepare condition for requests
 */


$where = '';
if (count($sql_fin) > 0) {
	$where = sprintf(" WHERE book_id IN (%s)", $sql_fin[0]);
	for ($i = 1; $i < count($sql_fin); ++$i)
		$where .= sprintf(" AND book_id IN (%s)", $sql_fin[$i]);
}

/* Declare some aliases */
$alias_authors = "authors";
$alias_aids = "authors_ids";

$query_count = "SELECT COUNT(book_id) FROM books_tb $where";
$query_data = "SELECT book_id, book_name, book_path," .
	" array_agg(author_name) AS $alias_authors, array_agg(author_id) AS $alias_aids " . 
	" FROM abfull_tb $where GROUP BY book_id, book_name, book_path ORDER BY book_name;";

/*
 * Get max and current page.
 */
$cres = db_query_ex($link, $query_count);

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

$res = db_query_ex($link, $query_data);

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
		$author_list = trim($row[$alias_authors], '{} ');
		$author_list = str_replace('"', '', $author_list);
		$author_list = explode(',', $author_list);
	
		$author_id_list = trim($row[$alias_aids], '{} ');
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
