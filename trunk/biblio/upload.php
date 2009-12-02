<?php
require_once('include/lib.php');

/* -------------------Функции для загрузки книги -------------------- */
/****************************************************************/

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
	$dir = substr(strtolower($name), 0, 2) . '/';
	return book_path_prefix . "$dir";
}


/*
 * то же что и translit только переводит все буквы в нижний регистр
 */
function low_translit($str)
{
	return strtolower(translit($str));
}

function make_array($str)
{
	$list = explode(',', $str);
	$res = '';

	for ($i = 0; $i < count($list); ++$i)
		$res .= sprintf("'%s',", trim($list[$i]));
	$res = trim($res, ',');
	return "ARRAY[$res]";
}

/*
 *
 */
if (!(isset($_POST['book_title']) && isset($_POST['book_author']) &&
	isset($_POST['book_desc']) && isset($_POST['book_volume']) &&
	isset($_POST['book_publish']) && isset($_POST['book_year']) &&
	isset($_POST['book_isbn'])))
		die("No parameters");

$link = db_connect() or die(pg_last_error());

$name = pg_escape_string($link, $_POST['book_title']);
$authors = pg_escape_string($link, $_POST['book_author']);
$desc = pg_escape_string($link, $_POST['book_desc']);
$volume = pg_escape_string($link, $_POST['book_volume']);
$publish = pg_escape_string($link, $_POST['book_publish']);
$year = pg_escape_string($link, $_POST['book_year']);
$isbn = pg_escape_string($link, $_POST['book_isbn']);

/*
 * Пробуем вытащить расширение файлов
 */
$or_name = basename(pg_escape_string($link, $_FILES['book_file']['name']));
$pos = mb_strrpos($or_name, '.', -1, 'utf8');
if ($pos === FALSE || mb_strlen($pos, 'utf8') > 4)
	$ext = '';
else
	$ext = mb_substr($or_name, $pos);
/* Размер загруженного файла */
$sz = $_FILES['book_file']['size'];
/* Путь к файлу */
$pathdir = get_book_path($name);
$path = $pathdir . strtolower($name) . $ext;

$query = sprintf("SELECT ADDBOOK('%s', %s, %d, '%s', '%s', %d, '%s', '%s', '%s', '%s', %d);",
		$name, make_array($authors), $volume, $desc, $publish, $year,
		$isbn, 'admin', $path, 'unknown', $sz);
echo $query;
$res = pg_query($link, $query) or die(pg_last_error());

/* Создаем директорию если ее нету */
mkdir($pathdir, 0777, true);
/* Переносим загруженный файл в библиотеку */
move_uploaded_file($_FILES['book_file']['tmp_name'], $path);

pg_close($link);
?>
<p><b>Книга успешно добавлена</b></p>
