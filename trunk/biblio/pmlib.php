<?php
require_once('logins.php'); /* don't upload this file to svn!!! */
require_once('mylib.php');
?>
<div id = "vmenu">Дополнительное меню</div>
<div id = "main" >
<?php

$link = libdb_connect();

/*
 * Не стоит это конечно показывать пользователю, но
 * пока об этом думать рано, не так ли?
 */
if (!$link)
	die('Could not connect: ' . mysql_error());

/*
$query = sprintf("SELECT %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s from biblio;",
		title, author, publish, volume, year, isbn, descr,
		posted, imgpath, size, pages, who);
 */
$query = "SELECT * FROM biblio;";

$resource = mysql_query($query);

if (!$resource) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($resource))
	echo make_bookdiv($row);

?>
</div>
