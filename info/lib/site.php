<?php
require_once('include/site.php');

function print_sidebar()
{
?>
<div id = "vmenu">
	<p>Навигация</p>
	<ul>	
	<li><a href="<?php echo info_url; ?>">Информация о кафедре</a></li>
	<li><a href="<?php echo info_url . "/staff.php"; ?>">Сотрудники кафедры  </a></li>
	<li><a href="<?php echo info_url . "/contact.php"; ?>">Контакты</a></li>
	</ul>
</div>
<?php
}

?>

