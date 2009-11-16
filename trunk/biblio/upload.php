<?php

require_once('logins.php');
require_once('libdb.php');

/*
 * upload_book - Загружает книгу. Возвращает false если произошла ошибка.
 * 		 В liberror_msg записывается сообщение.
 */


/*
 * check_input - Проверяет параметры при загрузки книги на сервер.
 * 		 А также в глобальную переменную записывает сообщение об
 * 		 ошибке.
 * 		 Предварительно стерев её предыдущее содержание.
 */
function check_input()
{
	/* Глобальные переменные это нехорошо, но как проще я не знаю. */
	unset($GLOBALS[liberror_msg]);

	return  check_input_name($_POST[book_title]) &&
		check_input_author($_POST[book_author]) &&
		check_input_volume($_POST[book_volume]) &&
		check_input_year($_POST[book_year]) &&
		check_input_publish($_POST[book_publish]) &&
		check_input_isbn($_POST[book_isbn]) &&
		check_input_desc($_POST[book_desc]) &&
		check_input_pages($_POST[book_pages]);
}


define('ext_error', "Загружать можно только pdf, djv, dvju.", true);
define('server_error_msg',
	"Извините, ошибка на стороне сервера. Попробуйте позже");

function upload_book($book_name)
{
	$book_real_name = low_translit($book_name);
	$path = get_book_path(low_translit($book_real_name));

	/* Сперва создадим директорию, если её еще нету. */
	if (!file_exists($path) && !mkdir($path, 0744, true)) {
		/* Опять таки пока временно */
		$GLOBALS[liberror_msg] = "Cannot create directory.";
		return false;
	}
	/* 
	 * Теперь попробуем узнать расширение. Если это не pdf или djvu,
	 * то отклоним загрузку файла.
	 */
	$exts = array("pdf" => 0, "djvu" => 0, "djv" => 0);

	$user_name = basename($_FILES[book_file]['name']);
	/*
	 * Поскольку я не знаю, можно ли как-то узнать кодировку у клиента,
	 * то для вычисления длины строки используется strlen. Не знаю
	 * правильно ли это. Жду предложений поэтому поводу.
	 */
	$len = strlen($user_name);
	$pos = strrpos($user_name, '.');
	if (($len < 5) || ($pos === false) || ($len - $pos > 6)) {
		$GLOBALS[liberror_msg] = ext_error;
		return false;
	}

	$ext = substr($user_name, $pos + 1, $len - $pos);
	if (!array_key_exists($ext, $exts)) {
		$GLOBALS[liberror_msg] = ext_error;
		return false;
	}
	/*
	 * Наконец-то дошли до загрузки файла, хотя фактически конечно
	 * она уже давно началась.
	 */
	$book_server_name = $path . $book_real_name;
	/*
	 * Проверяем нет ли уже такого файла.
	 * FIXME: 
	 * 	Eсли есть то отклоняем загрузку. В будущем планирую
	 * 	заменить это на проверку не идентична ли книга существующей.
	 * 	Т.е. необходимо будет сделать sql-запрос по авторам, тому,
	 * 	кол-ву страниц, размеру.
	 */
	if (file_exists($book_server_name . $ext)) {
		$GLOBALS[liberror_msg] = "Такая книга уже есть.";
		return false;
	}
	/*
	 * "Загружаем" файл.
	 */
	if (!move_uploaded_file($_FILES[book]['tmp_file'], $book_server_name)) {
		$GLOBALS[liberror_msg] = server_error_msg;
		return false;
	}
	/*
	 * Добавляем в базу данных сведения о книге.
	 */
	$link = libdb_connect();
	if (!$link)
		die('Could not connect to db: ' . mysql_error());

	$query_params = array(
		db_title => $_POST[book_title],
		db_author => $_POST[book_author],
		db_volume => $_POST[book_volume],
		db_publish => $_POST[book_publish],
		db_year => $_POST[book_year],
		db_isbn => $_POST[book_isbn],
		db_descr => $_POST[book_desc],
		db_pages => $_POST[book_pages],
		db_who => "admin", /* Серега мне нужна функция!!! */
		db_posted => "NOW()",
		db_size => $_FILES[book_file]['size']
	);
	$params = prepare_sql_insert_query($query_params);
	$query = sprintf('INSERT INTO biblio %s VALUES %s;',
			 $params['fields'], $params['values']);

	$ret = true;

	if (!mysql_query($query, $link)) {
		$GLOBALS[liberror_msg] = server_error_msg;
		unlink($book_server_name);
		$ret = false;
	}
	mysql_close($link);
	
	return $ret;
}

/*
 * load_book - загружает файлы(предположительно книгу, картинку(обложку).
 */
function load_book()
{
	return true;
}
/*
if (check_input($_POST) && load_book($_POST))
	echo 'Success';
else
	echo $lib_error_msg;
 */

?>
