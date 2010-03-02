<?php
require_once('include/auth.php');
init_logins();

require_once('include/site.php');
require_once('include/lib.php');
require_once('./lib/dbconst.php');
require_once('./lib/libview.php');
require_once('./lib/site.php');

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
	
	return (int)($all / $limit) + (($all % $limit) ? (1) : (0));
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

	$ldots = ($left == 1) ? ('') : ('... ');
	$rdots = ($right == $max) ? ('') : ('...');

	$res = "<div id=\"pagelist\">$ldots";
	for ($i = $left; $i <= $right; ++$i)
		if ($i == $curr) {
			$res .= "$i ";
		} else {
			$tmp = tag_href($curr_url . htmlspecialchars("&pg=$i"), "$i ");
			$res .= str_replace(htmlspecialchars("list.php&"), "list.php?", $tmp);
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

$biblio_url = sprintf("%s/list.php?", lib_url);
/*
 * Connect to database
 */
$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

/*
 * Check search parameters first!
 */
$sql_req = array("s_book" => "SELECT book_id FROM books_tb WHERE book_name ILIKE '%%%s%%'",
		 "s_author" => "SELECT book_id FROM abfull_tb WHERE author_name ILIKE '%%%s%%'",
		 "s_dep" => "SELECT book_id FROM dbfull_tb WHERE dep_name ILIKE '%%%s%%'",
		 "author_id" => "SELECT book_id FROM ab_tb WHERE author_name = '%s'",
	 	 "dep_id" => "SELECT book_id FROM db_tb WHERE dep_name = '%s'");

$s_params = array("s_book", "s_author", "s_dep");

$params = array();
$values = array();
if (check_parameters($s_params, $_REQUEST)) {
	$params = $s_params;
	$values = $_REQUEST;
} else if (isset($_GET['author_id'])) {
	$params = array('author_id');
	$values = array('author_id' => $_GET['author_id']);
} else if (isset($_GET['dep_id'])) {
	$params = array('dep_id');
	$values = array('dep_id' => $_GET['dep_id']);
}

$sql_fin = array();
for ($i = 0; $i < count($params); ++$i) {
	$curr = trim($values[$params[$i]]);
	if ($curr != '') {
		$curr = pg_escape_string($link, $curr);
		$sql_fin[] = sprintf($sql_req[$params[$i]], $curr);
	}
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

/*
 * Getting ip-address of client
 */
if ($where == '')
	$where_fmt = ' WHERE %s ';
else
	$where_fmt = ' AND %s ';

$ip_remote = $_SERVER['REMOTE_ADDR'];
if (!check_ipaddress($ip_remote)) {
	    $where .= sprintf($where_fmt, 'book_ispublic');
}

/* Declare some aliases */
$alias_authors = "authors";

$query_count = "SELECT COUNT(book_id) FROM books_tb $where";

/*
 * Get max and current page.
 */
$cres = pg_query($link, $query_count);
if (!$cres)
	include_once('include/html_db_error.php');

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
$query_data = "SELECT book_id, book_name, book_path," .
	" array_agg(author_name) AS $alias_authors " . 
	" FROM abfull_tb $where GROUP BY book_id, book_name, book_path ORDER BY book_name" .
	" LIMIT $limit OFFSET $offset;";

$res = pg_query($link, $query_data);
if (!$res)
	include_once('include/html_db_error.php');
?>
<!DOCTYPE html>
<html>
<?php
print_head("Библиотека");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<?php
if (pg_num_rows($res) == 0) {
	write_user_message("По данному запросу книг нету.");
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
		$author_list = clean_string($row[$alias_authors], '{} ');
		$author_id_list = $author_list;

		$author_list = explode(',', $author_list);
		$author_id_list = explode(',', $author_id_list);
		/*
		 * Prepare to print
		 */
		$fields = array();
		$fields[] = make_href($biblio_url . htmlspecialchars("author_id="),
				      $author_list, $author_id_list);
		$fields[] = tag_href(sprintf("%s", lib_url . "/" .
			htmlspecialchars("desc.php?book_id=" . $row['book_id'])),
		       		      $row['book_name']);
		$fields[] = tag_href($row['book_path'], "Скачать");
	
		print_table_row($classes[$i++ % 2], $fields);
	}
	print("</table></div>");
	print($pg_list);
}

?>

</div>
</body>
</html>
