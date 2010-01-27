<div id = "vmenu">
	<p>Навигация</p>
	<ul>	
		<li><a href="?page=pmlib&amp;view=list">Список книг</a></li>
		<li><a href="?page=pmlib&amp;view=search">Поиск книги</a></li>
		<li><a href="?page=pmlib&amp;view=add">Добавить книгу</a></li>
	</ul>
</div>
<div id = "main">
<?php
if (isset($_GET['uploadfile']) && $_GET['uploadfile'] == 1) {
	include("upload.php");
}

$scripts = array("list"   => "./biblio/views/list.php",
		 "desc"   => "./biblio/views/desc.php",
		 "add"    => "./biblio/views/add.php",
	 	 "search" => "./biblio/views/search.php");

/*
 * Define Global urls
 */
$biblio_url = htmlspecialchars("?page=pmlib&view=biblio");

if (isset($_REQUEST['view']) && array_key_exists(($view = $_REQUEST['view']), $scripts))
	include_once($scripts[$view]);
else
	include_once($scripts['list']);
?>
</div>

