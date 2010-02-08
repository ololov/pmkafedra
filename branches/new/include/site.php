<?php
include_once('include/auth.php');
include_once('include/defs.php');

/*
 * Elements of site
 */

/*
 * print <head> ... <title>$title</title> ... </head>
 */
function print_head($title)
{
	/* for me this is a little strange, but it works! */
?>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<title><?php echo $title; ?></title>
	<link rel = "stylesheet" type = "text/css" href = "<?php echo css_style_url; ?>" />
</head>
<?php
}/* print_head */

/*
 * print sites header
 */
function print_header()
{
?>
<div id = "container">
	<div id = "head">
	<img src = "<?php echo images_url . "/logo.jpg"; ?>" class = "logo">
		<p><span class = "cfd">КАФЕДРА</span><br><span class = "app">ПРИКЛАДНОЙ МАТЕМАТИКИ</span></p>
	</div>
	<div class = "gmenu">
		<div class = "gmenucont">
			<ul  class = "wrpr">
			<li><a href = "<?php echo base_url; ?>">Главная страница</a></li>
			<li><a href = "<?php echo info_url; ?>">Информация о кафедре</a></li>
			<li><a href = "<?php echo stud_url; ?>">Студенту</a></li>
			<li><a href = "<?php echo work_url; ?>">Научная работа</a></li>
			<li><a href = "<?php echo news_url; ?>">Новости и события</a></li>
			<li><a href = "<?php echo lib_url; ?>">Библиотека</a></li> 
			<li><a href = "<?php echo forum_url; ?>">Форум</a></li>
			<li><a href = "<?php echo gallary_url; ?>">Галерея</a></li>
			</ul>
		</div>
	</div>
	<div id = "separator"></div>
</div>
<?php
} /* print_header */

function print_login_form()
{
	if (user_priv() == A_ANON_READ) {
?>
	<form method="POST" id="enter" action = "<?php echo $_SERVER['REQUEST_URI']; ?>">
	Логин:<br>
	<input type="text" name="login" />
	Пароль:<br>
	<input type="password" name="pass" /><br><br>
	<input type="submit" name="submit" value="Вход" class = "buttonSubmit">
</form>
<?php
	}
}

/*
 * CSS id for content div tag
 */
define('css_content_div', 'main', true);

?>
