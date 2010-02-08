<?php
require_once('include/access.php');
require_once('include/defs.php');

/*
 *
 */
function get_login($login, $pass)
{
	/*
	 * Здесь должна быть реализована
	 * авторизация по LDAP
	 */
	if ($login == 'prepod' && $pass == 'prepod') {
		$_SESSION['user'] = 'prepod';
		$_SESSION['priv'] = A_PREPOD;
		$_SESSION['full_name'] = 'Васюткин Василий Васильевич';
	}
}

function try_to_login()
{
	if (isset($_POST['login']) && isset($_POST['pass']))
		get_login($_POST['login'], $_POST['pass']);
	if (!isset($_SESSION['user'])) {
		$_SESSION['user'] = 'anonymous';
		$_SESSION['priv'] = A_ANON_READ;
		$_SESSION['full_name'] = 'Гость';
	}
}

/*
 * init_logins - Запускают аунтификацию. 
 * 	Так как	используются механизм сессий, то должна быть
 * 	запущена до операций вывода данных клиенту.
 */
function init_logins()
{
	session_start();
	try_to_login();
}

/*
 * redirect_user - Перенаправляет на главную, если у него
 * 		   нету $priv привилегии.
 */
function redirect_user($priv, $url)
{
	if (!(user_priv() & $priv)) {
		header("Location: $url");
		exit;
	}
}

/*
 * redirect_user_strong - то же что и redirect_user, только
 * 			  требует наличие требуемых
 * 			  всех привилегии.
 */
function redirect_user_strong($priv, $url)
{
	if (!((user_priv() & $priv) == $priv)) {
		header("Location: $url");
		exit;
	}
}

/*
 * Функции обертки
 */
function user_login()
{
	return $_SESSION['user'];
}

function user_name()
{
	return $_SESSION['full_name'];
}

function user_priv()
{
	return $_SESSION['priv'];
}

/*
 * priv_print - Выводит _$str_ если у текущего пользователя
 * 		есть _$priv_ привилегия.
 */
function priv_print($str, $priv)
{
	if (user_priv() & $priv)
		print($str);
}

?>
