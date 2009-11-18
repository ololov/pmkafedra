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
define('db_id', 'id', true);
define('db_title', 'name', true);
define('db_author', 'author', true);
define('db_volume', 'volume', true);
define('db_publish', 'publish', true);
define('db_year', 'year', true);
define('db_isbn', 'isbn', true);
define('db_descr', 'description', true);
define('db_posted', 'posted', true);
define('db_path', 'bookpath', true);
define('db_imgpath', 'imgpath', true);
define('db_size', 'sz', true);
define('db_pages', 'pages', true);
define('db_who', 'who', true);
define('db_depart', 'department', true);

/*
 * Имя хранимой процедуры, которая возвращает
 * полную информацию о книги.
 * Принимает один параметр - id книги.
 */
define('proc_book_info', 'get_book_info', true);
/*
 * Имя хранимой процедуры, которая возвращает
 * лишь некоторые поля из таблицы для представления
 * книг списком.
 */
define('proc_book_list', 'get_book_list', true);

?>
