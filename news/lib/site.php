<?php
require_once('include/defs.php');
require_once('include/auth.php');

function print_sidebar()
{
?>
<div id = "vmenu">
	<p>Навигация</p>
	<ul>
		<li><a href = "<?php echo news_url . "/all.php"; ?>">Все новости</a></li>
		<li><a href = "<?php echo news_url . "/sched.php"; ?>">Изменения в расписании</a></li>
		<li><a href = "<?php echo news_url . "/lib.php"; ?>">Новое в библиотеке</a></li>
<?php
priv_print("<li><a href = \"" . news_url . "/add.php\">Добавить новость</a></li>", A_ADD_BOOK);
?>
	</ul>
</div>
<?php
}
?>
