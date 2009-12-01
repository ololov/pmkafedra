<?php

require_once('include/logins.php');
require_once('include/lib.php');

/*
 * Database defenitions and
 * Column names from database tables
define('db_books', 'books_tb');
define('db_authors', 'authors_tb');
define('db_ab', 'ab_tb');
 */
/*
 * Синонимы полей таблиц в БД
 *
 */
define('book_id', 'book_id', true);
define('book_name', 'book_name', true);
define('book_vol', 'book_volume', true);
define('book_pub', 'book_publish', true);
define('book_year', 'book_year', true);
define('book_isbn', 'book_isbn', true);
define('book_desc', 'book_desc', true);
define('book_post', 'book_posted', true);
define('book_path', 'book_path', true);
define('book_face', 'book_face', true);
define('book_sz', 'book_size', true);
define('book_page', 'book_pages', true);
define('book_who', 'book_who', true);
define('book_dep', 'book_dep', true);

define('author_id', 'author_id');
define('author_name', 'author_name');
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
function get_book_info($book_id)
{
	$fmt = <<<EOF
SELECT
	*,
	ARRAY(SELECT author_id FROM ab_tb WHERE ab_tb.book_id = books_tb.book_id ORDER BY author_id) AS author_ids,
	ARRAY(SELECT author_name FROM ab_tb WHERE ab_tb.book_id = books_tb.book_id ORDER BY author_id) AS author_names
FROM
	books_tb
WHERE
	book_id = %d;
EOF;
	return sprintf($fmt, $book_id);
}

function get_query_list($from, $count)
{
	$query = <<<EOF
SELECT
	book_id,
	book_name,
	array_agg(author_id) AS author_ids,
	array_agg(author_name) AS author_names,
	(SELECT book_path FROM books_tb WHERE books_tb.book_id = ab_tb.book_id) AS book_path
FROM
	ab_tb
GROUP BY
	book_id, book_name
ORDER BY
	book_name
EOF;
	return "$query LIMIT $count OFFSET $from;";
}

function get_query_list_by_author($from, $count, $aid)
{
	$query = <<<EOF
SELECT
	book_id,
	book_name,
	array_agg(author_id) AS author_ids,
	array_agg(author_name) AS author_names,
	(SELECT book_path FROM books_tb WHERE books_tb.book_id = ab_tb.book_id) AS book_path
FROM
	ab_tb
GROUP BY
	book_id, book_name
HAVING
	%d = ANY(array_agg(author_id))
ORDER BY
	book_name
LIMIT %s OFFSET %d;
EOF;
	return sprintf($query, $aid, $count, $from);
}



?>
