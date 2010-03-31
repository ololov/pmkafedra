<?php

require_once('include/defs.php');
require_once('include/auth.php');

/*
 * Соединяется с сервером mysql и "выбирает" базу данных dbname
 */
function db_connect()
{
	$db_profile = ((user_priv() & A_PREPOD) == A_PREPOD) ? (db_writer) : (db_reader);
	$link = pg_connect($db_profile);

	if ($link)
		return $link;

	return false;
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

function path_worker_photo($path)
{
	return info_url . "/$path";
}

?>
