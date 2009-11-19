<?php
require_once('biblio/dbconst.php');

define('desc_path', '?page=pmlib&view=desc&book_id=', true);
define('books_table_row', 'odd', true);
/*
 * Local functions
 */

/*
 * Tags functions
 */
function tag_href($ref, $label)
{
	return "<a href=\"$ref\">$label</a>";
}

function table_field($val)
{
	return "<td>$val</td>";
}

function table_row($row)
{
	return "<tr>$row</tr>";
}


function make_book_list_entry($book, $class)
{
	if($class != "") {
		$class = "class=\"$class\"";
	}
	return sprintf("<tr $class>%s%s%s</tr>",
			table_field($book[db_authors]),
			table_field(tag_href(desc_path . $book[db_id],
				    $book[db_title])),
			table_field(tag_href($book[db_path], "Скачать")));
}

/*
 * Если тебе Юля что-то нужно изменить то меняй здесь!
 *
		<tr class = "odd">
			<td><a href ="" >Название и Автор книги</a></td>
			<td>Раздел</td>
			<td><a href ="" >скачать</a></td>
		</tr>
		<tr>
			<td><a href ="" >Название и Автор книги</a></td>
			<td>Раздел</td>
			<td><a href ="" >скачать</a></td>
		</tr>
		<tr class = "odd">
			<td><a href ="" >Название и Автор книги</a></td>
			<td>Раздел</td>
			<td><a href ="" >скачать</a></td>
		</tr>
		<tr>
			<td><a href ="" >Название и Автор книги</a></td>
			<td>Раздел</td>
			<td><a href ="" >скачать</a></td>
		</tr>

 */
$link = libdb_connect();
if (!$link)
	die('Could not connect: ' . mysql_error());

$query = get_sql_book_list_query();

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
