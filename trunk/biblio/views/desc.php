<?php
require_once('./biblio/dbconst.php');
require_once('./biblio/libview.php');

/*
 * Default values
 */
define("noface", "smile.jpg", true);
define("max_desc_len", 1000, true); /* Максимальная длина описания */

/*
 * Local functions
 */
/*
 * make_bookinfo - параметры книги (название, автор, издание, ...)
 * tag:
 * 	<div class=bookinfo>
 * 	<table>
 * 	<parameter name> | <parameter value>
 * 	</table>
 * 	</div>
 */
function make_bookinfo($book)
{
	global $biblio_url;

	$list_path = $biblio_url . htmlspecialchars("&view=list&author_id=");
	$template =
		"<div id=\"%s\"><table>%s%s%s%s</table></div>";
	$alist = explode(',', clean_string($book['author_names']));
	$ilist = explode(',', clean_string($book['author_ids']));

	$dlist = clean_string($book['dep_names']);
	if ($dlist != '') {
		$dlist = explode(',', $dlist);
		$dilist = explode(',', clean_string($book['dep_ids']));

		$dep_path = $biblio_url .
			htmlspecialchars("&view=list&dep_id=");
		$dlist = make_href($dep_path, $dlist, $dilist);
		$dlist = make_row("Раздел(ы)", $dlist);
	}

	return sprintf($template, 'bookinfo',
			make_book_title($book),
			make_row("Автор(ы)", make_href($list_path, $alist, $ilist)),
			make_book_pyi($book), $dlist);
}

function make_row($name, $value)
{
	return table_row(table_field($name) . table_field($value));
}

/*
 * make_book_pyi 
 */
function make_book_pyi($book)
{
	$out = "";

	if (isset($book[book_volume]))
		$out = make_row("Том ", $book[book_volume]);
	if (isset($book[book_pub]))
		$out .= make_row("Издательство", $book[book_pub]);
	if (isset($book[book_year]))
		$out .= make_row("Год выпуска ", (string)$book[book_year]);
	if (isset($book[book_isbn]))
		$out .= make_row("ISBN ", $book[book_isbn]);
	if (isset($book[book_dep]))
		$out .= make_row("Раздел", $book[book_dep]);


	/* не могут быть нулевыми */
	$out .= make_row("Выложено ", convert_dateformat($book[book_posted]));
	$out .= make_row("Кем выложено ", $book[book_who]);

	if (isset($book[book_sz]))
		$out .= make_row("Размер ", book_size($book));
	if (isset($book[book_pages]))
		$out .= make_row("Страниц ", $book[book_pages]);

	return $out;
}

/*
 * make_bookdesc - описание книги в теге div.
 */
function make_bookdesc($book, $maxlen)
{
	if (!isset($book[book_desc])) {
		$desc = "Нет описания.";
	} else {
		$desc = $book[book_desc];
		$len = mb_strlen($desc, 'utf8');
		
		if (isset($maxlen) && ($len > $maxlen)) {
			$desc = mb_strcut($desc, 0, max_desc_len, 'utf8');
			$desc .= "...";
		}
	}

	return sprintf("<div id=\"%s\"><b>Описание:</b><p>%s</div>",
			'bookdesc', $desc);
}

/*
 * book_size - Размер книги в байтах, килобайтах или мегабайтах
 */
function book_size($book)
{
	$out = "";
	$bytes = $book[book_sz];

	/* размер исчисляется в мегабайтах? */
	if (($sz = $bytes / 1048576) >= 1) {
		$out = "Мб";
	/* размер исчисляется в килобайтах? */
	} else if (($sz = $bytes / 1024) >= 1) {
		$out = "Кб";
	} else {
		/* Что-то на книгу не похоже ... наверное txt файл. */
		$sz = $bytes;
		$out = "байт";
	}

	$sz = ((int)($sz * 100) / 100);
	return "$sz $out";
}

function make_book_title($book)
{
	$title = $book[book_name];

	return sprintf("<tr><td>Название: </td><td><b>%s</b></td></tr>",
			$title);
}



/*
 * make_bookimg - тег img.
 */
function make_bookimg($book)
{
	$alt = "";
	$src = $book[book_face];
	if (isset($src) && file_exists($src)) {
		$alt = "Обложка";
	} else {
		$alt = "Нет обложки";
		//$src = noface;
		$src = 'images/Books.png';
	}

	return sprintf("<img class=\"%s\" alt=\"%s\" src=\"%s\">",
	       		'bookface', $alt, $src);	
}
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
	return sprintf("<div id=\"%s\">%s%s%s</div>",
			'book',
			make_bookimg($book),
			make_bookinfo($book),
			make_bookdesc($book, NULL));
}

?>
<?php

/*
 * Connection to database. Throw exception if fail.
 */
$link = db_connect_ex();

if (!(isset($_GET['book_id']) && is_numeric($book_id = $_GET['book_id'])))
	$book_id = -1;

$id_agg = "array_agg(author_id)";
$author_agg = "array_agg(author_name)";

$groups ="tr.book_id, tb.book_name, book_volume, book_publish," .
	 "book_who, book_desc, book_year, book_desc, book_isbn, book_posted," .
	 "book_path, book_face, book_size, book_pages";
$query = "SELECT $groups," .
	 "array_agg(tr.author_id) AS author_ids, " . 
	 "array_agg(ta.author_name) AS author_names, " .
	 "ARRAY(SELECT dep_id FROM db_tb WHERE book_id = $book_id) AS dep_ids, " .
	 "ARRAY(SELECT dep_name FROM dbfull_tb WHERE book_id = $book_id) AS dep_names " .
	 "FROM ab_tb AS tr INNER JOIN books_tb AS tb ON(tr.book_id = tb.book_id) " .
	 "INNER JOIN authors_tb AS ta ON(tr.author_id = ta.author_id) " .
	 "WHERE tr.book_id = $book_id GROUP BY $groups;";

$resource = db_query_ex($link, $query);

if (pg_num_rows($resource) == 0) {
	write_user_message("Нет такой книги");
} else {
	$row = pg_fetch_assoc($resource);
	echo make_bookdiv($row);
}

/*echo "<p align=center><b>Извините, запрощенной книги нету.</b></p>";*/

?>
