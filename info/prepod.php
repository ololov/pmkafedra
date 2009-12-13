<?php include_once("info/sidebar.php"); ?>
<div id = "main"> 
<?php
	require_once('include/lib.php');
	
	//!!! вставьте свой логин и пароль
	$link = db_connect() or die(pg_last_error());

	if (isset($_GET['id_pr']) && is_numeric($_GET['id_pr']))
	{
		$id_prep = $_GET['id_pr'];
		$resource = pg_query('SELECT * FROM prepod WHERE id='.$id_prep);
		$predmet = pg_query('SELECT predmet_name FROM predmet_info WHERE prepod='.$id_prep);
		if (!$resource) {
			echo "<p><b> Извините, ошибка на стороне сервера. Зайдите позже.</b><p>";
			echo "</div>";
			exit;
		} else {
			$prep = pg_fetch_assoc($resource);
			while($data = pg_fetch_row($predmet)){
				$pred[] = $data[0];
			}
			if ($prep['id'] != $_GET['id_pr']) {
				echo "<p><b> Такого преподавателя не существует.</b></p>";
				echo "</div>";
				exit;
			}
		}
	}
?>


	<?php $photo = "info/photo/".$prep['id'].".jpg"; ?>
	<img  src="<?php echo $photo; ?>" class = "prep_photo">
	<p><span class="name"><?php echo $prep['lname']." ".$prep['fname']." ".$prep['sname']?> </span></p>
	<span class="subtit"> Ученая степень и должность: </span> <?php echo $prep['post']?><br>
	<span class="subtit"> Дисциплины:</span> <?php echo implode(",",$pred)?><br>
	<span class="subtit"> Область научных интересов:</span> <?php echo $prep['scentific_int']?><br>
	<span class="subtit"> Контакты:</span> <?php echo $prep['contact']?><br>
	<span class="subtit"> Информация:</span><br><span class='info_about'><?php echo $prep['about'] ?></span>
</div>	
