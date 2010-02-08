<?php
require_once('include/defs.php');
require_once('include/auth.php');

function print_sidebar()
{
?>
<div id = "vmenu">
	<p>Навигация</p>
	<ul>
		<li><a href = "<?php echo lib_url . "/list.php"; ?>">Список книг</a></li>
		<li><a href = "<?php echo lib_url . "/search.php"; ?>">Поиск книг</a></li>
<?php
priv_print("<li><a href = \"" . lib_url . "/add.php\">Добавить книгу</a></li>", A_ADD_BOOK);
?>
	</ul>
</div>
<?php
}
?>
