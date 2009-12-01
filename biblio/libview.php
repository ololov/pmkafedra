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
 * URLs
 */
define('desc_path', '?page=pmlib&amp;view=desc&amp;book_id=', true);
define('list_path', '?page=pmlib&amp;view=list&amp;a_id=', true);
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
 * В местное представление даты и времени.
 * FIXME:
 * 	Пока заглушка
 */
function convert_dateformat($mysqltime)
{
	return $mysqltime;
}

/*
 * Создает ссылки дла фильтрации
 * по параметру
 */
function make_href($ref_prefix, $name_list, $param_val_list)
{
	$str = "";
	for ($i = 0; $i < count($name_list); ++$i)
		$str .= tag_href($ref_prefix . $param_val_list[$i],
				$name_list[$i]) . ", ";
	$str = trim($str);
	$str = trim($str, ',');
	return $str;
}

/*
 *
 */
function clean_string($str)
{
	return str_replace(array('"', '{', '}'), '', $str);
}

?>
