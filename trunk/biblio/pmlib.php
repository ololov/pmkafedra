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
$scripts = array("list" => "biblio/views/list.php",
		 "desc" => "biblio/views/desc.php",
		 "add"  => "biblio/views/add.php");

if (isset($_GET['view']) && array_key_exists(($view = $_GET['view']), $scripts))
	include_once($scripts[$view]);
else
	include_once($scripts['list']);
?>
</div>

