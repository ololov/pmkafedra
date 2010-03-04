<?php
require_once('include/site.php');

function print_sidebar()
{
?>
<div id = "vmenu">
	<p>Навигация</p>
	<ul>
		<li><a href = "<?php echo about_url . "/index.php"; ?>">Информация о сайте</a></li>
		<li><a href = "<?php echo about_url . "/we.php";?>">Разработчики</a></li>
	</ul>
</div>
<?php
}

?>

