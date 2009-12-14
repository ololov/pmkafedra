<?php

	include_once('authlib.php');

	if (isset($_POST['login']) && isset($_POST['pwd']) &&
		!empty($_POST['login']) && !empty($_POST['login']))
	{
		$tmp = new AuthTXT();
		$tmp->login($_POST['login'], $_POST['pwd']);
	}
	die('{status:1,txt:"index.php?page=info"}');

//	header("Location: /index.php?page=info");

?>
