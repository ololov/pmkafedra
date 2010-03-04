<!DOCTYPE html>
<html>
	<?php
		include_once('include/site.php');
		include_once('./lib/site.php');

		print_head("О сайте");
	?>
	<body>
		<?php
			print_header();
			print_sidebar();
		?>
		
		<div id = "<?php echo css_content_div; ?>">
		<p class = "tit">Разработчики сайта</p>
		<div>
			<ul>
				<li>Бояркин Сергей</li>
				<li>Исаев Сергей</li>
				<li>Исубилов Руслан</li>
				<li>Ким Максим</li>
				<li>Куликова Юлия</li>
				<li>Потемкина Светлана</li>
				<li>Радькова Мария</li>
			</ul>
		</div>
	</body>
</html>
