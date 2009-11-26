<?php
require_once('auth/authlib.php');

if (!(isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])))
	echo <<<EOF
<div>
<form method="POST" action = "auth/auth.php" id="enter">
	Логин:<br>
	<input type="text" name="login" />
	Пароль:<br>
	<input type="text" name="pwd" /><br><br>
	<input type="submit" name="submit" value="Вход" class = "buttonSubmit">
</form>
</div>
EOF
?>
