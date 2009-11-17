<?php

require_once('include/lib.php');
require_once('biblio/libdb.php');

/* параметры в теге <form> ... </form> */
define('book_title', 'user_book_title', true);
define('book_author', 'user_book_authors', true);
define('book_volume', 'user_book_volume', true);
define('book_year', 'user_book_year', true);
define('book_publish', 'user_book_publish', true);
define('book_isbn', 'user_book_isbn', true);
define('book_desc', 'user_book_desc', true);
define('book_pages', 'user_book_pages', true);

define('book_file', 'user_book', true);
define('book_face', 'user_book_face', true);


/*
 * ---------- Функции для проверки входных параметров. -----------------
 */


define('liberror_msg', 'liberror_msg', true);


define("min_book_name_len", 2, true);
define("max_book_name_len", 64, true);

/*
 * check_input_name - 
 */

function check_input_name($name)
{
	$pos = mb_strpos($name, '/\r\n', 0, 'utf8');

	if (!$pos) {
		$GLOBALS[liberror_msg] = "Недопустимое название книги.";
		return false;
	}

	$len = mb_strlen($name, 'utf8');

	$ret = (min_book_name_len <= $len) && ($len <= max_book_name_len);
	if (!$ret)
		$GLOBALS[liberror_msg] = "Слишком короткое или длинное имя";

	return $ret;
}

/*
 * check_input_author - Проверяет на допустимую длину в имени автора.
 * FIXME:
 * 	Пока только глупая проверка. Проверяет лишь чтобы составные части
 * 	полного имени, а точнее их длина была не меньше 2.
 * 	В будущем расчитываю придумать более универсальную проверку.
 */
define('max_author_fullname_len', 128);

function check_input_author($authors)
{
	$alist = explode(',', trim($authors)); /* Автор может быть и не один */

	for ($i = 0; $i < count($alist); $i++) {
		$len = mb_strlen($alist[$i], 'utf8');

		if ($len > max_author_fullname_len) {
			$GLOBALS[liberror_msg] = "Слишком длинное имя автора.";
			return false;
		}
		$author = explode(' \t', $alist[$i]);
		if (!check_full_name($author)) 
			return false;
	}
	return true;
}

function check_full_name($author)
{
	$format = array("имя", "отчество", "фамилия");

	for ($i = 0; $i < count($author); ++$i)
		if (mb_strlen($author[$i]) < 2) {
			$GLOBALS[liberror_msg] =
				"Слишком короткое ".$format[$i];
			return false;
		}

	return true;
}

/*
 * check_input_volume - что делает? ... see the source code :)
 */
function check_input_volume($volume)
{
	$ret = is_int($volume) || ($volume == ""); /* Volume - опциональное */
	if (!$ret)
		$GLOBALS[liberror_msg] = "Поле \"том\" должно быть числом";
	return $ret;
}

/*
 * check_input_year
 */
function check_input_year($year)
{
	$ret = is_int($year) && ($year > 0) || ($year == "");
	if (!$ret)
		$GLOBALS[liberror_msg] = "Недопустимый год.";
	return $ret;
}


/*
 * check_input_publish - 
 *
 * В liberror_msg записывается сообщение.
 */
function check_input_publish($publish)
{
	/* Честно говоря не знаю, предназначена ли она для этой цели. */
	$ret = strlen($publish) < 255;
	if (!$ret)
		$GLOBALS[liberror_msg] = "Слишком длинное значение publish.";
	return $ret;
}

/*
 * check_input_isbn - 
 *
 * В liberror_msg записывается сообщение.
 */
function check_input_isbn($isbn)
{
	$ret = strlen($publish) < 255;
	if (!$ret)
		$GLOBALS[liberror_msg] = "Слишком длинное значение isbn.";
	return $ret;
}

/*
 * check_input_desc - Проверка на длину.
 * 
 * В mysql стоит ограничение на тип TEXT в 64кб. С учётом того
 * что пользователь может внести sql инъекции и их придется экранировать
 * то допустимый размер должен быть как минимум вдвое меньше.
 *
 * В liberror_msg записывается сообщение.
 */
function check_input_desc($desc)
{
	$sz = strlen($desc); 
	$ret = $sz < 32768;
	if (!$ret)
		$GLOBALS[liberror_msg] = "Слишком длинное описание.";

	return $ret;
}

/*
 * check_input_pages - Что делает? ... see source code. :)
 */
function check_input_pages($pages)
{
	$ret = is_int($pages) && ($pages > 3);
	/*
	 * Немного глупо проверять на больше или меньше. Если
	 * посчитаете её лишней удалю без проблем.
	 */
	if (!$ret)
		$GLOBALS[liberror_msg] = "Слишком мало страниц.";
	return $ret;
}

/* -------------------Функции для загрузки книги -------------------- */
/*
 * get_book_path - Возварщает путь где книга может быть сохранена.
 * name - имя должно содержать только цифры и латиницу.
 */
define('book_path_prefix', 'biblio/books/', true);

function get_book_path($name)
{
	/*
	 * Для того, чтобы книги не сваливать в одну кучу(читай директорию) 
	 * создадим промежуточные директории имя которых будет равно первым
	 * двум буквам имени
	 * Пока других идей нету.
	 */
	$dir = "";
	if (!ctype_alnum($name[0]))
		$name[0] = '_';
	if (!ctype_alnum($name[1]))
		$name[1] = '_';
	$dir = substr($name, -2) . '/';
	return book_path_prefix . dir;
}

/*
 * get_image_path - Пока что синоним get_book_path. В будущем может быть
 * 		    не так.
 */
function get_image_path($name)
{
	return get_book_path($name);
}

/****************************************************************/

function prepare_sql_insert_query($values)
{
	$fields = "(";
	$fvalues = "(";

	foreach ($values as $key => $val) {
		if ($fvalues == NULL)
			continue;

		$fields .= "$key,";
		$fvalues .= "\'". mysql_real_escape_string($val) . "\',";
	}
	$fields = trim($fields, ',') . ')';
	$fvalues = trim($fvalues, ',') . ')';

	return array("fields" => $fields, "values" => $fvalues);
}

/*
 * translit:
 * 	Переводит кириллицу в латиницу.
 *
 * Мягкий и твердый знак просьба не трогать.
 */
function translit($str)
{
	$trans = array(	'А' => 'A',	'Б' => 'B',	'В' => 'V',
			'Г' => 'G',	'Д' => 'D',	'Е' => 'E',
		       	'Ё' => 'YO',	'Ж' => 'ZH',	'З' => 'Z',
			'И' => 'I',	'Й' => 'Y',	'К' => 'K',
			'Л' => 'L',	'Н' => 'N',	'О' => 'O',
			'П' => 'P',	'Р' => 'R',	'С' => 'S',
			'Т' => 'T',	'У' => 'U',	'Ф' => 'F',
			'Х' => 'H',	'Ц' => 'TZ',	'Ч' => 'CH',
			'Ш' => 'SH',	'Щ' => 'SH',	'Ъ' => '',
			'Ы' => 'I',	'Ь' => '',	'Э' => 'E',
			'Ю' => 'YU',	'Я' => 'YA',	'а' => 'a',
			'б' => 'b',	'в' => 'v',	'г' => 'g',
			'д' => 'd',	'е' => 'e',	'ё' => 'yo',
			'ж' => 'zh',	'з' => 'z',	'и' => 'i',
			'й' => 'y',	'к' => 'k',	'л' => 'l',
			'м' => 'm',	'н' => 'n',	'о' => 'o',
			'п' => 'p',	'р' => 'r',	'с' => 's',
			'т' => 't',	'у' => 'u',	'ф' => 'f',
			'х' => 'h',	'ц' => 'tz',	'ч' => 'ch',
			'ш' => 'sh',	'щ' => 'sh',	'ъ' => '',
			'ы' => 'i',	'ь' => '',	'э' => 'e',
			'ю' => 'yu',	'я' => 'ya');
	return strtr($str, $trans);
}

/*
 * то же что и translit только переводит все буквы в нижний регистр
 */
function low_translit($str)
{
	return strtolower(translit($str));
}



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
