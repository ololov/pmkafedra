<?php

require_once('include/defs.php');

/*
 * Соединяется с сервером mysql и "выбирает" базу данных dbname
 */
function db_connect()
{
	$link = pg_connect(db_profile);

	if ($link)
		return $link;

	return false;
}

function db_connect_ex()
{
	$link = pg_connect(db_profile);

	if (!$link)
		throw new Exception(pg_last_error());

	return $link;
}

function db_query_ex($link, $query)
{
	$res = pg_query($link, $query);
	if (!$res)
		throw new Exception(pg_last_error());
	return $res;
}

function write_user_message($msg)
{
	printf("<div id = \"umsg\">%s</div>", $msg);
}

/*
 *
 */
function clean_string($str)
{
	return trim(str_replace(array('"', '{', '}'), '', $str));
}

?>
