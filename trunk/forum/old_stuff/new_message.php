<?php 
	$thread_id = "";
	if (isset($_GET['thread']))
		$thread_id = $_GET['thread'];
	else 
		die('Внутренняя ошибка.');
?>

<div id = "forum_main">
	<p class = "tit">Новое сообщение</p>
	<form id = "form_forum_mes">
		<fieldset>
			<legend>Сообщение</legend>
			<p>Заголовок сообщения <input type = text class = "text_mes"></input></p>
			<p>Текст сообщения <textarea cols = 50 rows = 10 class = "text_mes"></textarea></p>
			<input type="hidden" name ="thread_id" value="<?php echo $thread_id; ?>">
		</fieldset>
		<input type = submit value = "Отправить" class = "buttonSubmit">
	</form>
</div>
