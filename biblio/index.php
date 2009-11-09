<?php
require_once('mylib.php');
require_once('logins.php'); /* don't upload this file to svn!!! */
?>
<html>
	<head>
		<title>Главная страница</title>
		<meta content ="charset = utf-8">
		<link rel = "stylesheet" type = "text/css" href = "../style.css" />
	</head>
	<body>
		<div id = "container">
			<div id = "head"><h6><span>Московский Государственный Технический Университет Гражданской Авиации</span></h6>
					 <h1>БИБЛИОТЕКА КАФЕДРЫ ПРИКЛАДНОЙ МАТЕМАТИКИ</h1>
			</div>
			<div class = "gmenu">
				<div class = "gmenucont">
					<ul  class = "wrpr">
						<li><a href = "..">Информация о кафедре</a></li>
						<li><a href = "..">Студенту</a></li>
						<li><a href = "..">Научная работа</a></li>
						<li><a href = "..">Новости и события</a></li>
						<li><a href = "#">Библиотека</a></li> 
						<li><a href = "..">Форум</a></li>
						<li><a href = "..">Галерея</a></li>
					</ul>
				</div>
			</div>
			<div id = "separator"></div>
			<div id = "vmenu">Дополнительное меню</div>
			<div id = "main" >
<?php
$link = mysql_connect("localhost", dbuser, dbpassword);

/*
 * Не стоит это конечно показывать пользователю, но
 * пока об этом думать рано, не так ли?
 */
if (!$link)
	die('Could not connect: ' . mysql_error());

if (!mysql_select_db(dbname, $link))
	die('Could not select db: ' . mysql_error());

$query = "SELECT * from biblio;";
$resource = mysql_query($query);

if (!$resource) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($resource))
	echo make_bookdiv($row);

?>
</div>
		</div>
	</body>
</html>
