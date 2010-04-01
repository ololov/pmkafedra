<?php
require_once('include/lib.php');

/*
 * Возвращает название книги в теге <a>...</a>
 * $row == pg_fetch_assoc($result) !!!
 * Warning: псевдонимы должны совпадать.
 */
function get_book_name($row)
{
	return sprintf("<a href = \"%s%d\">%s</a>",
			lib_url . htmlspecialchars("/desc.php?book_id="),
			$row['book_id'],
			$row['book_name']);
}

function __make_list($row, $alias, $wrapper, $prefix)
{
	if ($wrapper == '') {
		$wb = "<$wrapper>";
		$we = "</$wrapper>";
	} else {
		$wb = '';
		$we = '';
	}

	$alist = explode(',', clean_string($row[$alias]));
	for ($i = 0; $i < count($alist); ++$i)
		$res .= sprintf("$wb<a href = \"$prefix%s\">%s</a>$we ",
				urlencode($alist[$i]),
				$alist[$i]);
	return $res;
}

function get_book_author_list($row)
{
	return __make_list($row, 'author_name', 'span',
			lib_url . htmlspecialchars("/list.php?author_id="));
}

/*
 */
function get_book_list_by_disc($name)
{
	return sprintf("<a href = \"%s%s\">%s</a>",
			lib_url . htmlspecialchars("/list.php?disc_id="),
			urlencode($name), $name);
}

?>
