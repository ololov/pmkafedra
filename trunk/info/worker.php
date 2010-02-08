<?php
include_once('include/site.php');
include_once('include/lib.php');
include_once('./lib/site.php');

if (!isset($_GET['wid']))
	$wid = -1;
else
	$wid = $_GET['wid'];

$link = db_connect();
if (!$link)
	include_once('include/html_db_error.php');

$wid = pg_escape_string($wid);

$sql = "SELECT * FROM workers_tb WHERE worker_id = $wid;";
$res = pg_query($link, $sql);

?>
<!DOCTYPE html>
<html>
<?php
print_head("Кафедра");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
<?php
if (pg_num_rows($res) == 0) {
	write_user_message("Нет такого сотрудника");
} else {
	$worker = pg_fetch_assoc($res);
	$photo = $worker['worker_photo'];
}

if (file_exists($photo)) {
?>
<img  src="<?php echo $photo; ?>" class = "worker_photo">
<?php
}
?>
<p><span class="name"><?php echo $worker['worker_name']; ?></span></p>
<span class="subtit"> Ученая степень и должность: </span> <?php echo $worker['worker_seat'];?><br>
<span class="subtit"> Область научных интересов:</span> <?php echo $worker['worker_interests'];?><br>
<span class="subtit"> Контакты:</span> <?php echo $worker['worker_contact'];?><br>
<span class="subtit"> Информация:</span><span class="info_about"><?php echo " " . $worker['worker_about'];?></span>
</div>	
</body>
</html>
