<?php
	include_once('include/auth.php');
	init_logins();

	include_once('include/site.php');
	include_once('include/lib.php');

	$link = db_connect();
	if (!$link)
		include_once('include/html_db_error.php');

	$sql = "SELECT news_text, news_header, news_date ".
	       "FROM news_tb ORDER BY news_date DESC LIMIT 3;";

	$res = pg_query($link, $sql);
	if (!$res)
		include_once('include/html_db_error.php');

	$sql = "SELECT book_id, CAST(book_posted AS DATE), book_name, book_who ".
	       "FROM books_tb ORDER BY book_posted LIMIT 3;";
	$bres = pg_query($link, $sql);
	if (!$bres)
		include_once('include/html_db_error.php');

	$fmt = "<tr>" .
		"<td class = 'news_head'>%s</td>".
		"<td class = 'new_day'>%s</td>".
		"</tr>".
		"<tr class = 'desc'>".
		"<td colspan=2 class = 'news_des'>%s</td>".
		"</tr>";
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
					while ($row = pg_fetch_assoc($res)) {
						printf ($fmt,
							$row['news_header'], 
							$row['news_date'], 
							$row['news_text']
							);
					}			
				}
			?>
			</table>
			</div>		
			<div id = "main_lib">
				<p class = "tit">Новые книги</p>
				<div id = "main_news">
				<table id = "news_table">
<?php
	while (($row = pg_fetch_assoc($bres))) {
		$bid = $row['book_id'];
		printf("<tr><td>%s</td><td>%s</td><td>%s</td></tr>",
			$row['book_posted'],
			sprintf("<a href = \"%s\">%s</a>",
				lib_url . htmlspecialchars("/desc.php?book_id=$bid"),
				$row['book_name']),
			$row['book_who']);
	}
?>

			</table>
			</div>		
			</div>
		</div>
	</body>
</html>
