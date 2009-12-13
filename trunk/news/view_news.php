<?php
	include_once('sidebar.php');
	$n = $_GET['n'];
	if (is_numeric($n)) {
		include_once('news/lib_news.php');
		include_once('include/lib.php');
		$connetc = db_connect();
		$query = get_str($n);
		$data  = get_data($query);
		echo "<div id = 'main'><table id = 'news_table'>";
		for ($i = 0; $i < count($data); $i++) {
			echo "<tr><td class = 'news_head'>".$data[$i]['headline']."</td>";
			echo "<td class = 'new_day'>".$data[$i]['news_date']."</td></tr>";
			echo "<tr class = 'desc'><td colspan=2 class = 'news_des'>".$data[$i]['news_desc']."</td></tr>";
		}
		echo "</table></div>";
	}
	else {
		if ($n == 'a') include_once ('news/news.php');
	}
?>
