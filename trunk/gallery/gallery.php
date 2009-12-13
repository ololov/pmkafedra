<?php
include('./gallery/g_sidebar.php');

$g = array('study'       => './gallery/study/i.php',
	   'aleksandrov' => './gallery/aleksandrov/i.php',
	   'ekvator'     => './gallery/ekvator/i.php',
	   'kafedra'     => './gallery/kafedra/i.php',
	   'maks'        => './gallery/maks/i.php',
	   'newyear'     => './gallery/newyear/i.php',
	   'peinball'    => './gallery/peinball/i.php',
	   'main'	 => './gallery/main.php'
	  );

if (isset($_GET['g']) && array_key_exists($_GET['g'], $g))
	include_once($g[$_GET['g']]);
else
	include_once($g['main']);  

?>
