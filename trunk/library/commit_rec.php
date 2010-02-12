<?php
include_once('include/auth.php');
init_logins();
redirect_user(A_ADD_BOOK, lib_url);

if (!isset($_POST['bid']) || empty($_POST['bid']))
	redirect_user(0, lib_url);

include_once('include/lib.php');

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

$bid = pg_escape_string($_POST['bid']);
$rectext = pg_escape_string($_POST['rec_text']);

$dset = array();
foreach ($_POST as $key => $value)
	if (preg_match('/disc[0-9]+/', $key) && !empty($value))
		$dset[] = sprintf("'%s'", pg_escape_string($value));

if (count($dset))
	$sqlp = sprintf("ARRAY[%s]", implode(',', $dset));
else
	$sqlp = 'NULL';

$query = sprintf("SELECT ADDREC(%d, '%s', '%s', %s);", $bid, user_login(), $rectext, $sqlp);
$res = pg_query($link, $query);
if ($res)
	redirect_user(0, lib_url . htmlspecialchars("/desc.php?book_id=$bid"));

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
	write_user_message(pg_last_error());
?>
</div>
</body>
</html>
