<?php
include_once('include/site.php');
include_once('./lib/site.php');
	date_default_timezone_set('Europe/Moscow');

	function print_to_file($content){
		$filename = "content.txt";
		$max_size = 8 * 1024;  // 8 Kb
		$date = date("d-F-Y H:i l");
		$fp = @fopen($filename,"at") or die ('Ошибка на сторне сервера, приносим свои извенения');
		flock($fp, LOCK_EX);
		if(strlen($content) > $max_size){?>
			<script language="javascript">
				alert('Введенный текст слишком большой');
			</script>
		<?php 
			return;
		}else {
			fwrite($fp,"$date \n $content \n\n\n"); ?>
			<script language="javascript">
				alert('Данные успешно переданы');
			</script>
		<?php }
		fclose($fp);
	}

	if(!empty($_REQUEST['content'])){
		$content = trim($_REQUEST['content']);
		print_to_file($content);
	}

?>
<!DOCTYPE html>
<html>
<?php
print_head("О сайте");
?>
<body>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">
	<p class="tit">Информация о сайте</p>
	<ul>
		<li>ПУНКТ 1</li>
		<li>ПУНКТ 2</li>
	</ul> 


	<p class="tit">Озывы и предложения</p>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="POST">
	<textarea rows="15" name="content"></textarea> 
	<br><br>
	<input type="submit" value="Отправить">
</div>
</body>
</html>
