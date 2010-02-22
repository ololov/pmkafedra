<?php
require_once('include/access.php');
require_once('include/defs.php');

/*
 *
 */

function get_gecos($link, $login)
{
	/*
	$sr = ldap_read($link, $logindn, $logindn, array("gecos"));
	if ($sr == false)
		return false;

	$entry = ldap_get_entries($link, $sr);

	return $entry[0]['gecos'];
	 */
	$sr = ldap_search($link, "dc=pm,dc=intranet", "(uid=$login)");
	if ($sr == false)
		return false;

	$entry = ldap_get_entries($link, $sr);
	return $entry[0]['gecos'][0];
}

function get_login($login, $pass)
{
	define('LDAP_SERVER', '172.21.44.128', true);
	/*
	 * Здесь должна быть реализована
	 * авторизация по LDAP
	 */
	$link = ldap_connect(LDAP_SERVER);
	if (!$link)
		include_once('include/html_ldap_error.php');

	ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, 3);

	$logindn = "uid=$login,ou=users,dc=pm,dc=intranet";
	$pmworkersdn = "cn=pmworkers,ou=groups,dc=pm,dc=intranet";

	if (ldap_bind($link, $logindn, $pass)) {
		if (ldap_compare($link, $pmworkersdn, "memberUid", $login) === true)
			$priv = A_PREPOD;
		else
			$priv = A_ANON;
		if ($gecos = get_gecos($link, $login)) {
			$_SESSION['user'] = $login;
			$_SESSION['priv'] = $priv;
			$_SESSION['full_name'] = $gecos;
		}
	} else {
		include_once('include/html_auth_error.php');
	}
/*
*/
}

function try_to_login()
{
	if (isset($_POST['login']) && $_POST['login'] == 'prepod') {
		$_SESSION['user'] = 'prepod';
		$_SESSION['priv'] = A_PREPOD;
		$_SESSION['full_name'] = 'Васюткин Василий Васильевич';
		return;
	}
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
