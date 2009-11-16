<?php

/*
 * Style sheets
 */
define("bookclass", "book", true);
define("imgclass", "bookface", true);
define("descclass", "bookdesc", true);
define("bookinfo", "bookinfo", true);

define("books_table_row", "odd", true);
define("books_table", 'tit', true);
/*
 * Default values
 */
define("noface", "smile.jpg", true);
define("max_desc_len", 1000, true); /* Максимальная длина описания */


/*
 * Наверное Юля, за следующие функции ты меня возненавидишь.
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

	return sprintf($template, bookinfo,
			make_book_title($book),
			make_book_authors($book),
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

	if (isset($book[db_volume]))
		$out = make_row("Том: ", $book[db_volume]);
	if (isset($book[db_publish]))
		$out .= make_row("Издательство:", $book[db_publish]);
	if (isset($book[db_year]))
		$out .= make_row("Год выпуска: ", (string)$book[db_year]);
	if (isset($book[db_isbn]))
		$out .= make_row("ISBN: ", $book[db_isbn]);


	/* не могут быть нулевыми */
	$out .= make_row("Выложено: ", convert_dateformat($book[db_posted]));
	$out .= make_row("Кем выложено: ", $book[db_who]);

	if (isset($book[db_size]))
		$out .= make_row("Размер: ", book_size($book));
	if (isset($book[db_pages]))
		$out .= make_row("Страниц: ", $book[db_pages]);

	return $out;
}

function convert_dateformat($mysqltime)
{
	return $mysqltime;
}

/*
 * book_size - Размер книги в байтах, килобайтах или мегабайтах
 */
function book_size($book)
{
	$out = "";
	$bytes = $book[db_size];

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
	$title = $book[db_title];

	return sprintf("<tr><td>Название: </td><td><b>%s</b></td></tr>",
			$title);
}

function make_book_authors($book)
{
	$str = "";
	$author = "Автор";

	$alist = explode(',', $book[author]);
	
	if (count($alist) == 1)
		$author .= ": ";
	else
		$author .= "ы: ";

	for ($i = 0; $i < count($alist); $i++)
		$str .= $alist[$i] . ", ";
	$str = trim($str, ", ");

	return make_row($author, $str);
}

/*
 * make_bookdesc - описание книги в теге div.
 */
function make_bookdesc($book, $maxlen)
{
	$desc = $book[db_descr];

	if (!isset($desc)) {
		$desc = "Нет описания.";
	} else {
		$len = mb_strlen($desc, 'utf8');
		
		if (isset($maxlen) && ($len > $maxlen)) {
			$desc = mb_strcut($desc, 0, max_desc_len, 'utf8');
			$desc .= "...";
		}
	}

	return sprintf("<div class=\"%s\"><b>Описание:</b><p>%s</div>",
			descclass, $desc);
}

/*
 * make_bookimg - тег img.
 */
function make_bookimg($book)
{
	$alt = "";
	$src = $book[db_imgpath];

	if (!isset($src)) {
		$alt = "Нет обложки";
		$src = noface;
	} else {
		$alt = "Обложка";
	}

	return sprintf("<img class=\"%s\" alt=\"%s\" src=\"%s\">",
	       		imgclass, $alt, $src);	
}

?>
