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

/*
 * Наверное Юля, за следующие функции ты меня возненавидишь.
 */

function make_bookdiv($book)
{
	$img_tag = make_bookimg($book);
	$desc = make_desc($book);
	$info = make_bookinfo($book);

	return sprintf("<div class=\"%s\">%s%s%s</div>",
			bookclass, $img_tag, $info, $desc);
}

function make_bookinfo($book)
{
	$template =
		"<div class=\"%s\"><table>%s%s%s</table></div>";

	return sprintf($template, bookinfo,
			make_book_title($book),
			make_book_authors($book),
			make_book_pyi($book));
}

function make_book_pyi($book)
{
	$brow = "<tr><td>";
	$erow = "</td></tr>";
	$out = "";

	if (isset($book[publish]))
		$out = "$brow Издательство: </td><td>" .
			$book[publish] . "$erow";
	if (isset($book[year]))
		$out .= "$brow Год выпуска: </td><td>" .
			(string)$book[year] . "$erow";
	if (isset($book[isbn]))
		$out .= "$brow ISBN: </td><td>" .
			$book[isbn] . "$erow";
	if (isset($book[posted]))
		$out .= "$brow Выложено: </td><td>" .
			(string)$book[posted] . "$erow";

	$out .= "$brow Кем выложено: </td><td>" .
		$book[who] . "$erow";

	return $out;
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

	return sprintf("<tr><td>%s</td><td>%s</td></tr>",
			$author, $str);
}

function make_desc($book)
{
	$desc = $book[descr];

	if (!isset($desc))
		$desc = "Нет описания.";

	return sprintf("<div class=\"%s\"><b>Описание:</b><p>%s</div>",
			descclass, $desc);
}

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

	/*	return "<img alt=\"$alt\" src=\"$src\">";*/
	return sprintf("<img class=\"%s\" alt=\"%s\" src=\"%s\">",
	       		imgclass, $alt, $src);	
}

?>

