<?php
require_once('include/auth.php');
init_logins();
redirect_user(A_ADD_BOOK, lib_url);

include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

/*
 *
 */
$params = array('book_title', 'book_author', 'book_desc',
		'book_volume', 'book_publish', 'book_year',
		'book_isbn', 'book_dep');

for ($i = 0; $i < count($params); ++$i)
	if (!isset($_POST[$params[$i]]))
		redirect_user(0, lib_url . "/add.php");

?>
<!DOCTYPE html>
<html>
<?php
include_once('include/site.php');
include_once('./lib/site.php');

print_head("Библиотека");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<?php
require_once('include/lib.php');

function write_and_die()
{
	write_user_message(pg_last_error());
	printf("</div></body></html>");
	exit(1);
}

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
			'ю' => 'yu',	'я' => 'ya',	' ' => '_');
	return strtr($str, $trans);
}
/*
 * get_book_path - Возварщает путь где книга может быть сохранена.
 * name - имя должно содержать только цифры и латиницу.
 */
define('book_path_prefix', 'books/', true);

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
		$res .= sprintf("'%s',", trim($list[$i], ' "'));
	$res = trim($res, ',');
	return "ARRAY[$res]";
}

$val = array();
for ($i = 0; $i < count($params); ++$i)
	$val[] = trim(pg_escape_string($link, $_POST[$params[$i]]));

if (is_uploaded_file($_FILES['book_file']['tmp_name'])) {
	$name = $val[0];
	/*
	 * Пробуем вытащить расширение файлов
	 */
	$or_name = basename(pg_escape_string($link, $_FILES['book_file']['name']));
	$or_name = low_translit($or_name);
	$ext = pathinfo($or_name, PATHINFO_EXTENSION);
	//$ext = $ext['extension'];
	/* Размер загруженного файла */
	$sz = $_FILES['book_file']['size'];
	/* Путь к файлу */
	$pathdir = get_book_path($name);
	$lname = strtolower($name);
	$path = "$pathdir$lname.$ext";
	for ($i = 1; file_exists($path); ++$i)
		$path = "$pathdir$lname($i).$ext";
	
	$authors = make_array($val[1]);
	$desc = ($val[2] == '') ? ('NULL') : ("'$val[2]'");
	$volume = ((int)$val[3] == 0) ? ('NULL') : ($val[3]);
	$publish = ($val[4] == '') ? ('NULL') : ($val[4]);
	$year = ($val[5] == '') ? ('NULL') : ($val[5]);
	$isbn = ($val[6] == '') ? ('NULL') : ($val[6]);
	
	$deps = $val[7];
	if ($deps != '')
		$deps = make_array($val[7]);
	else
		$deps = 'NULL';
	/*
	 */
	$user = user_name();
	
	$query = sprintf("SELECT ADDBOOK('%s', %s, '%s', %d, '%s', %s, %s, %s, %s, %s, %s);",
			$name, $authors, $user, $sz, $path, $volume, $desc,
			$publish, $year, $isbn, $deps);
	/* Заносим данные в БД */
	$res = pg_query($link, $query);
	if (!$res)
		write_and_die();
	
	/* Создаем директорию если ее нету */
	mkdir($pathdir, 0777, true);
	/* Переносим загруженный файл в библиотеку */
	move_uploaded_file($_FILES['book_file']['tmp_name'], $path);
	
	pg_close($link);

	$id = pg_fetch_array($res);
	$url = $id[0];
	$href = sprintf("<a href = %s>Перейти к описанию книги</a>",
			lib_url . htmlspecialchars("/desc.php?book_id=$url"));

	write_user_message("<p>Книга успешно загружена<p>$href");
} else {
	$err = $_FILES['book_file']['error'];
	if ($err == UPLOAD_ERR_INI_SIZE)
		$err = "Размер книги превышает лимит.";
	write_user_message("Загрузка завершилась неудачно: $err");
}
?>

</div>
</body>
</html>
