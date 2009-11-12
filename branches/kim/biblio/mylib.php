<?php

/*
 * Мягкий и твердый знак просьба не трогать.
 */
function translit($str)
{
	$trans = array(	'А' => 'A',	'Б' => 'B',	'В' => 'V',
			'Г' => 'G',	'Д' => 'D',	'Е' => 'E',
		       	'Ё' => 'YO',	'Ж' => 'ZH',	'З' => 'Z',
			'И' => 'I',	'Й' => 'Y',	'К' => 'K',
			'Л' => 'L',	'Н' => 'N',	'О' => 'O',
			'П' => 'P',	'Р' => 'R',	'С' => 'S',
			'Т' => 'T',	'У' => 'U',	'Ф' => 'F',
			'Х' => 'H',	'Ц' => 'TZ',	'Ч' => 'CH',
			'Ш' => 'SH',	'Щ' => 'SH',	'Ъ' => '',
			'Ы' => 'I',	'Ь' => '',	'Э' => 'E',
			'Ю' => 'YU',	'Я' => 'YA',	'а' => 'a',
			'б' => 'b',	'в' => 'v',	'г' => 'g',
			'д' => 'd',	'е' => 'e',	'ё' => 'yo',
			'ж' => 'zh',	'з' => 'z',	'и' => 'i',
			'й' => 'y',	'к' => 'k',	'л' => 'l',
			'м' => 'm',	'н' => 'n',	'о' => 'o',
			'п' => 'p',	'р' => 'r',	'с' => 's',
			'т' => 't',	'у' => 'u',	'ф' => 'f',
			'х' => 'h',	'ц' => 'tz',	'ч' => 'ch',
			'ш' => 'sh',	'щ' => 'sh',	'ъ' => '',
			'ы' => 'i',	'ь' => '',	'э' => 'e',
			'ю' => 'yu',	'я' => 'ya');
	return strtr($str, $trans);
}

function low_translit($str)
{
	return strtolower(translit($str));
}

/*
 * Database defenitions
 */
define("dbname", "clericsu_kafedrapm", true);

/*
 * Column names from database tables
 */
define("title", "name", true);
define("volume", "volume", true);
define("author", "author", true);
define("publish", "publish", true);
define("year", "year", true);
define("isbn", "isbn", true);
define("descr", "description", true);
define("posted", "posted", true);
define("imgpath", "imgpath", true);
define("size", "size", true);
define("pages", "pages", true);
define("who", "who", true);

/*
 * Style sheets
 */
define("bookclass", "book", true);
define("imgclass", "bookface", true);
define("descclass", "bookdesc", true);
define("bookinfo", "bookinfo", true);

/*
 * Default values
 */
define("noface", "smile.jpg", true);
define("max_desc_len", 1000, true); /* Максимальная длина описания */
/*
 * Наверное Юля, за следующие функции ты меня возненавидишь.
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

function table_row($name, $value)
{
	return "<tr><td>$name</td><td>$value</td></tr>";
}

/*
 * make_book_pyi 
 */
function make_book_pyi($book)
{
	$out = "";

	if (isset($book[volume]))
		$out = table_row("Том: ", $book[volume]);
	if (isset($book[publish]))
		$out .= table_row("Издательство:", $book[publish]);
	if (isset($book[year]))
		$out .= table_row("Год выпуска: ", (string)$book[year]);
	if (isset($book[isbn]))
		$out .= table_row("ISBN: ", $book[isbn]);


	/* не могут быть нулевыми */
	$out .= table_row("Выложено: ", convert_dateformat($book[posted]));
	$out .= table_row("Кем выложено: ", $book[who]);

	if (isset($book[size]))
		$out .= table_row("Размер: ", book_size($book));
	if (isset($book[pages]))
		$out .= table_row("Страниц: ", $book[pages]);

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
	$bytes = $book[size];

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
	$title = $book[title];

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
/*
	return sprintf("<tr><td>%s</td><td>%s</td></tr>",
			$author, $str);
 */
	return table_row($author, $str);
}

/*
 * make_bookdesc - описание книги в теге div.
 */
function make_bookdesc($book)
{
	$desc = $book[descr];

	if (!isset($desc)) {
		$desc = "Нет описания.";
	} else {
		$len = mb_strlen($desc, 'utf8');
		
		if ($len > max_desc_len) {
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
	$src = $book[imgpath];

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

