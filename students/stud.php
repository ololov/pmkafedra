<?php
include('./students/s_sidebar.php');

$deps = array('gos' => './students/disc/discipl.php',
	      'sched' => './students/schedule/schedule.php',
	      'disc'  => './students/classes.php');

if (isset($_GET['dep']) && array_key_exists($_GET['dep'], $deps))
	include_once($deps[$_GET['dep']]);
else
	include_once($deps['gos']);

?>
