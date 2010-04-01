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
 * Создает ссылки дла фильтрации
 * по параметру
 */
function make_href($ref_prefix, $name_list, $param_val_list)
{
	$str = "";
	for ($i = 0; $i < count($name_list); ++$i)
		$str .= tag_href($ref_prefix . urlencode($param_val_list[$i]),
				$name_list[$i]) . ", ";
	$str = trim($str);
	$str = trim($str, ',');
	return $str;
}


function print_table_row($class, $fields)
{
	if ($class != "")
		$class = "class=\"$class\"";

	$row = '';
	for ($i = 0; $i < count($fields); ++$i)
		$row .= "<td>" . $fields[$i] . "</td>";

	print("<tr $class>$row</tr>");
}

function check_ipaddress($ip_remote)
{
	/* stupid but works */
	if ($ip_remote == "127.0.0.1")
		return TRUE;
	$ip_part = explode('.', $ip_remote);
	if ($ip_part[0] != 172 || $ip_part[1] != 21 ||
	    $ip_part[2] != 42 || ($ip_part[3] < 65) ||
	    ($ip_part[3] > 127))
	    	return FALSE;
	return TRUE;
}

?>
