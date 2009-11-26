<?php

require_once('include/logins.php');

/*
 * Соединяется с сервером mysql и "выбирает" базу данных dbname
 */
function libdb_connect()
{
	$link = mysql_connect("localhost", dbuser, dbpassword);

	if (!$link)
		return $link;

	if (mysql_set_charset('utf8', $link) && mysql_select_db(dbname, $link))
		return $link;

	mysql_close($link);
	return false;
}

?>
