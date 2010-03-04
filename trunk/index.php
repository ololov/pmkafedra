<?php
	include_once('include/auth.php');
	init_logins();

	include_once('include/site.php');
	include_once('include/lib.php');

	$link = db_connect();
	if (!$link)
		include_once('include/html_db_error.php');

	$sql = "SELECT news_text, news_header, news_date FROM news_tb ORDER BY news_date LIMIT 3;";

	$res = pg_query($link, $sql);
	if (!$res)
		include_once('include/html_db_error.php');
?>

<!DOCTYPE html>
<html>
	<?php print_head("Главная страница");?>
	<body>
		<?php	print_header();?>
		<div id = "vmenu">
		<?php	print_login_form();?>
		</div>
		<div id = "<?php echo css_content_div; ?>">
			<div id = "main_news">
			<table id = "news_table">
			<?php
				if (pg_num_rows($res) == 0) {
					write_user_message("Ресурс не добавлен");
				} else {
					$fmt = "<tr>
						<td class = 'news_head'>%s</td>
						<td class = 'new_day'>%s</td>
						</tr>
						<tr class = 'desc'>
						<td colspan=2 class = 'news_des'>%s</td>
						</tr>";
					while ($row = pg_fetch_assoc($res)) {
						printf ($fmt,
							$row['news_header'], 
							date("d.m.Y H:i", $row['news_date']), 
							$row['news_text']
							);
						$i++;
					}			
				}
			?>
			</table>
			</div>		
			<div id = "main_lib">
				<p class = "tit">Новые книги</p>
				Макс, сюда списком нужно вывести три самые свежие книги 
				из твоей библиотеки :)
				(И не спрашивай, почему я не сделала это сама!)
			</div>
		</div>
	</body>
</html>
