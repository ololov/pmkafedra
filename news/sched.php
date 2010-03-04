<?php
	require_once('include/auth.php');
	init_logins();
?>

<?php
	include_once('include/lib.php');

	$link = db_connect();
	if (!$link)
		include_once('include/html_db_error.php');

	$sql = "SELECT news_text, news_header, news_date FROM news_tb WHERE news_type = 'Изменения в расписании' ORDER BY news_date;";

	$res = pg_query($link, $sql);
	if (!$res)
		include_once('include/html_db_error.php');
?>

<!DOCTYPE html>
<html>
	<?php
		include_once('include/site.php');
		include_once('./lib/site.php');

		print_head("Новости");
	?>
	<body>
		<?php
			print_header();
			print_sidebar();
		?>

		<div id = "<?php echo css_content_div; ?>">
			<p class = "tit">Новости</p>
			<table id = "news_table">
			<?php
				if (pg_num_rows($res) == 0) {
					write_user_message("Ресурс не добавлен");
				} else 
				{
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
							$row['news_date'], 
							$row['news_text']
						);
						$i++;
					}
				}
			?>
			</table>
		</div>
	</body>
</html>
