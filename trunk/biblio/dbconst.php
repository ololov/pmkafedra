<?php

require_once('include/logins.php');
require_once('include/lib.php');

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
 * Database defenitions and
 * Column names from database tables
 */

/*
 * Синонимы полей таблиц в БД
 */
define('db_id', 'book_id', true);
define('db_title', 'book_name', true);
define('db_authors', 'book_authors_name', true);
define('db_authors_id', 'book_authors_id', true);
define('db_volume', 'book_volume', true);
define('db_publish', 'book_publish', true);
define('db_year', 'book_year', true);
define('db_isbn', 'book_isbn', true);
define('db_descr', 'book_description', true);
define('db_posted', 'book_posted', true);
define('db_path', 'book_path', true);
define('db_imgpath', 'book_face_path', true);
define('db_size', 'book_size', true);
define('db_pages', 'book_pages', true);
define('db_who', 'book_who', true);
define('db_depart', 'book_department', true);

/*
 * Имя хранимой процедуры, которая возвращает
 * полную информацию о книги.
 * Принимает один параметр - id книги.
 *
define('proc_book_info', 'get_book_info', true);
 */
/*
 * Имя хранимой процедуры, которая возвращает
 * лишь некоторые поля из таблицы для представления
 * книг списком.
 *
define('proc_book_list', 'get_book_list', true);
 */

/*
 * Пока другого выхода я не вижу.
 */
function get_sql_book_info_query($book_id)
{
	$query = <<<EOF
SELECT
tb.id AS book_id,
tb.name AS book_name,
tb.volume AS book_volume,
tb.description AS book_desc,
tb.publish AS book_publish,
tb.year AS book_year,
tb.isbn AS book_isbn,
tb.posted AS book_posted,
tb.who AS book_who,
tb.bookpath AS book_path,
tb.imgpath AS book_face_path,
tb.sz AS book_size,
tb.pages AS book_pages,
tb.department AS book_department,
GROUP_CONCAT(ta.full_name SEPARATOR ", ") AS book_authors_name,
GROUP_CONCAT(ta.id SEPARATOR ", ") AS book_authors_id
FROM bib_books AS tb, bib_authors AS ta, bib_ab_relation AS tr 
WHERE tb.id = tr.id_book AND tr.id_author = ta.id AND tb.id =
EOF;
	return $query . sprintf(" %d;", $book_id);
}

function get_sql_book_list_query()
{
	return <<<EOF
SELECT
tb.id AS book_id,
tb.name AS book_name,
tb.volume AS book_volume,
tb.bookpath AS book_path,
tb.imgpath AS book_face_path,
tb.department AS book_department,
GROUP_CONCAT(ta.full_name SEPARATOR ", ") AS book_authors_name,
GROUP_CONCAT(ta.id SEPARATOR ", ") AS book_authors_id
FROM bib_books AS tb, bib_authors AS ta, bib_ab_relation AS tr 
WHERE tb.id = tr.id_book AND tr.id_author = ta.id GROUP BY tb.id;
EOF;
}

?>
