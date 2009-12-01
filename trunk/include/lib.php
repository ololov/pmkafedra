<?php

require_once('include/logins.php');

/*
 * Соединяется с сервером mysql и "выбирает" базу данных dbname
 */
function db_connect()
{
	$link = pg_connect(db_param);

	if ($link)
		return $link;

	pg_close($link);
	return false;
}

?>
