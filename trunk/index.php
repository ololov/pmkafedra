<!DOCTYPE html>
<html>
	<head>
		<title>Главная страница</title>
		<meta http-equiv="Content-Type" content="text/html; charset = UTF-8">
		<link rel = "stylesheet" type = "text/css" href = "style.css" />
	</head>
	<body>
		<div id = "container">
			<div id = "head">
				<img src = "images/logo.jpg" class = "logo">
				<p><span class = "cfd">КАФЕДРА</span><br><span class = "app">ПРИКЛАДНОЙ МАТЕМАТИКИ</span></p>
			</div>
			<div class = "gmenu">
				<div class = "gmenucont">
					<ul  class = "wrpr">
						<li><a href = "?page=info">Информация о кафедре</a></li>
						<li><a href = "">Студенту</a></li>
						<li><a href = "?page=science">Научная работа</a></li>
						<li><a href = "?page=news">Новости и события</a></li>
						<li><a href = "?page=pmlib">Библиотека</a></li> 
						<li><a href = "?page=forum">Форум</a></li>
						<li><a href = "?page=gallery">Галерея</a></li>
					</ul>
				</div>
			</div>
<?php
/*
 * $scripts - Хранит имена файлов соответствующие запрощенной страницы.
 * 		А именно то что передается из $_GET['page'] явл-ся ключом, а значение это имя файла(скрипта).
 */
$scripts = array('pmlib' => 'biblio/pmlib.php', 'info' => 'info/info.php', 'staff' => 'info/staff.php', 'contact' => 'info/contact.php', 
		 'list' => 'biblio/list.php');
$page = $_GET['page'];

if (isset($page) && array_key_exists($page, $scripts))
	include_once($scripts[$page]);
else
	include_once("main.php");

?>
		</div>
	</body>
</html>
