<html>
	<head>
		<title>Главная страница</title>
		<meta content ="charset = utf-8">
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
						<li><a href = "?page=stud">Студенту</a></li>
						<li><a href = "?page=science">Научная работа</a></li>
						<li><a href = "?page=news">Новости и события</a></li>
						<li><a href = "?page=pmlib">Библиотека</a></li> 
						<li><a href = "?page=forum">Форум</a></li>
						<li><a href = "?page=gallery">Галерея</a></li>
					</ul>
				</div>
			</div>
<?php
if (!isset($_GET['page']) || ($_GET['page'] == "")) {
	include('main.php');
} else {
	switch ($_GET['page']) {
	case "pmlib":
		include('biblio/pmlib.php');
	default:
		include('main.php');
	}
}
?>
		</div>
	</body>
</html>
