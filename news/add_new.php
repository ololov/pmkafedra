<?php
	require_once('include/auth.php');
	init_logins();
	redirect_user(A_ADD_NEWS, news_url);

	include_once('include/lib.php');

	$link = db_connect();
	if (!isset($link))
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

		<?php
			if (!empty($_POST['add'])) {
				if (empty($_POST['headline']) or empty($_POST['message'])){
					//redirect_user(A_ADD_BOOK, news_url . "/add.php");
					write_user_message ("Новость не была добавлена: не все поля формы заполнены");
				}
				else	{

					$type = $_POST['types'];
					$head = pg_escape_string($link,$_POST['headline']);
					$mess = pg_escape_string($link,$_POST['message']);
					$user = user_login();

					$query = sprintf("SELECT ADD_NEWS('%s', '%s', '%s', '%s');", $user, $type, $head, $mess);

					$res = pg_query($link, $query);
				
					if (!$res) {
						pg_last_error();
						echo "</div></body></html>";
						exit(1);
					}
					write_user_message("Новость успешно добавлена!");
				}
				pg_close($link);
			}
		?>
		</div>
	</body>
</html>
