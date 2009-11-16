<?php

require_once('logins.php');

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
 * Database defenitions
 */
define("dbname", "clericsu_kafedrapm", true);

/*
 * Column names from database tables
 */
define("db_title", "name", true);
define("db_volume", "volume", true);
define("db_author", "author", true);
define("db_publish", "publish", true);
define("db_year", "year", true);
define("db_isbn", "isbn", true);
define("db_descr", "description", true);
define("db_posted", "posted", true);
define("db_path", "bookpath", true);
define("db_imgpath", "imgpath", true);
define("db_size", "size", true);
define("db_pages", "pages", true);
define("db_who", "who", true);

/*
 * Соединяется с сервером mysql и "выбирает" базу данных dbname
 */
function libdb_connect()
{
	$link = mysql_connect("localhost", dbuser, dbpassword);

	if (!$link)
		return $link;

	$enc = mysql_client_encoding($link);
	if (mysql_set_charset($enc, $link) && mysql_select_db(dbname, $link))
		return $link;

	mysql_close($link);
	return false;
}

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


?>
