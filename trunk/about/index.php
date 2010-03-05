<?php
include_once('include/site.php');
include_once('./lib/site.php');
	date_default_timezone_set('Europe/Moscow');

	function print_to_file($content){
		$filename = "content.txt";
		$max_size = 8 * 1024;  // 8 Kb
		$date = date("d-F-Y H:i l");
		$fp = @fopen($filename,"at") or die ('Ошибка на сторне сервера, приносим свои извинения');
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
	Этот сайт начал разрабатываться в 2010 году в рамках лабораторных работ по дисциплине "Основы теории информационных сетей" 
	группой студентов пятого курса.
	Основными целями этого сайта являются:
	<ul>
		<li>Помощь студентам нашей кафедры в выборе учебного материала;</li>
		<li>Своевременное освещение интересных событий и важных новостей;</li>
		<li>Получение знаний и навыков в области проектирования сайтов, работы с базами данных и web-дизайна.</li>
	</ul> 
	Мы будем рады любой помощи, будь то участие в проекте, предложения по его усовершенствованию или отзыв
	о работе сайта.
	
	<p class="tit">Отзывы и предложения</p>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'];?>" method="POST">
	<textarea rows="15" name="content"></textarea> 
	<br><br>
	<input type="submit" value="Отправить">
</div>
</body>
</html>
