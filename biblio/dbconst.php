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
 * Database defenitions
 */

/*
 * Column names from database tables
 */
define("db_id", "id", true);
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


?>
