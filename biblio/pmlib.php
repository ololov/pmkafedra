<div id = "vmenu">
	<p>Навигация</p>
	<ul>	
		<li><a href="?page=pmlib&view=list">Список книг</a></li>
		<li><a href="?page=pmlib&view=search">Поиск книги</a></li>
		<li><a href="?page=pmlib&view=add">Добавить книгу</a></li>
	</ul>
</div>
<div id = "main">
<?php
$scripts = array("list" => "biblio/views/list.php",
		 "desc" => "biblio/views/desc.php");

$view = $_GET['view'];
if (isset($view) && array_key_exists($view, $scripts))
	include_once($scripts[$view]);
else
	include_once($scripts['list']);
?>
</div>

