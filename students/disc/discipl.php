<?php 
	$script = array('d1' => 'd1.php', 'd2' => 'd2.php','d3' => 'd3.php',
			'd4' => 'd4.php', 'd5' => 'd5.php','d6' => 'd6.php',
			'd7' => 'd7.php', 'd8' => 'd8.php','d9' => 'd9.php',
			'd10'=> 'd10.php','d11'=> 'd11.php');
	$pages = $_GET['pages'];
	if (isset($pages) && array_key_exists($pages, $script)){
		include_once($script[$pages]);
	} else {
		include_once('list.html');
	}
?>

