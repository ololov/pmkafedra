<?php
require_once('biblio/mylib.php');

/*
 * Local functions
 */

/*
 * make_bookdiv - generate book block in <div> tag.
 *
 * tag:
 * 	<div class=bookclass>
 * 	<image>
 * 	<book info>
 * 	<book description>
 * 	</div>
 */
function make_bookdiv($book)
{
	return sprintf("<div class=\"%s\">%s%s%s</div>",
			bookclass,
			make_bookimg($book),
			make_bookinfo($book),
			make_bookdesc($book));
}


?>
<?php

$link = libdb_connect();

/*
 * Не стоит это конечно показывать пользователю, но
 * пока об этом думать рано, не так ли?
 */
if (!$link)
	die('Could not connect: ' . mysql_error());

/*
$query = sprintf("SELECT %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s from biblio;",
		title, author, publish, volume, year, isbn, descr,
		posted, imgpath, size, pages, who);
 */
if (isset($_GET['book_id']))
	$book_id = mysql_real_escape_string($_GET['book_id']);
else
	$book_id = -1;

$query = sprintf("SELECT * FROM biblio WHERE id = %d", $book_id);

$resource = mysql_query($query);

if (!$resource) {
	echo "<p align=center><b>Извините, запрощенной книги нету.</b></p>";
} else {
	$row = mysql_fetch_assoc($resource);
	echo make_bookdiv($row);
}

?>
