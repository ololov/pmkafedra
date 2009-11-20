<?php
require_once('biblio/dbconst.php');
require_once('biblio/libview.php');

/*
 * Local functions
 */

function make_book_list_entry($book, $class)
{
	if($class != "") {
		$class = "class=\"$class\"";
	}
	
	$alist = explode(',', $book[db_authors]);
	$idlist= explode(',', $book[db_authors_id]);

	return sprintf("<tr $class>%s%s%s</tr>",
			table_field(make_href(list_path, $alist, $idlist)),
			table_field(tag_href(desc_path . $book[db_id],
				    $book[db_title])),
			table_field(tag_href($book[db_path], "Скачать")));
}

$link = libdb_connect();
if (!$link)
	die('Could not connect: ' . mysql_error());

if (isset($_GET['a_id']) && is_numeric($a_id = $_GET['a_id']))
	$query = getq_book_list_a($a_id);
else 
	$query = getq_book_list();


echo $query;

$resource = mysql_query($query);
if (!$resource) {
	die('Invalid query: ' . mysql_error());
}

?>

<p class="tit">Все книги</p>
<table>

<?php
$i = 0;
$classes = array(books_table_row, "");

while ($row = mysql_fetch_assoc($resource)) {
	echo make_book_list_entry($row, $classes[$i % 2]);
	++$i;
}

?>
</table>
