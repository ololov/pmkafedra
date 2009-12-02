<?php
require_once('biblio/dbconst.php');
require_once('biblio/libview.php');

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
	$template =
		"<div class=\"%s\"><table>%s%s%s</table></div>";
	$alist = explode(',', clean_string($book['author_names']));
	$ilist = explode(',', clean_string($book['author_ids']));

	return sprintf($template, 'bookinfo',
			make_book_title($book),
			make_row("Автор(ы)", make_href(list_path, $alist, $ilist)),
			make_book_pyi($book));
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

	return sprintf("<div class=\"%s\"><b>Описание:</b><p>%s</div>",
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
		$src = noface;
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
	return sprintf("<div class=\"%s\">%s%s%s</div>",
			'book',
			make_bookimg($book),
			make_bookinfo($book),
			make_bookdesc($book, NULL));
}

?>
<?php

/*
 * Не стоит это конечно показывать пользователю, но
 * пока об этом думать рано, не так ли?
 */
$link = db_connect() or die(pg_last_error());

if (!(isset($_GET['book_id']) && is_numeric($book_id = $_GET['book_id'])))
	$book_id = -1;

$query = get_book_info($book_id);
$resource = pg_query($link, $query);

if (!$resource || pg_num_rows($resource) == 0) {
	/*
	echo "<p><b>Извините, ошибка на стороне сервера.</b></p>";
	exit;
	 */
	die('Error : ' . pg_last_error());
}

$row = pg_fetch_assoc($resource);
echo make_bookdiv($row);

/*echo "<p align=center><b>Извините, запрощенной книги нету.</b></p>";*/

?>
