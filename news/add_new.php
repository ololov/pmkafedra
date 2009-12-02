<?php	
	include_once("news/lib_news.php");
	include_once("include/lib.php");
	if (!empty($_POST['add'])) {

		$connection = db_connect() or die(pg_last_error());

		$data = form_data(null);

		pg_insert($connection, "news", $data)
			or die ("2: Не удалось добавить новость");

		echo "Новость добавлена";
	}
?>
