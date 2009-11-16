<?php
require_once('mylib.php');
?>
<?php 
include_once("sidebar.php");
?>
<div id = "main">
	<p class="tit">Все книги</p>
	<table>
<?php
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

$query = "SELECT * FROM biblio;";

$resource = mysql_query($query);
if (!$resource) {
	die('Invalid query: ' . mysql_error());
}

$i = 0;
$classes = array(books_table_row, "");

while ($row = mysql_fetch_assoc($resource)) {
	echo make_book_list_entry($row, $classes[$i % 2]);
	++$i;
}

?>
	</table>
</div>

