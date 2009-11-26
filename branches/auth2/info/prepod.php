<?php include_once("info/sidebar.php"); ?>
<div id = "main"> 
<?php
	require_once('include/lib.php');
	
	//!!! вставьте свой логин и пароль
	$link = libdb_connect(dbuser, dbpassword) or die("Could not connect: ".mysql_error());

	if (isset($_GET['id_pr']))
	{
		$id_prep = mysql_escape_string($_GET['id_pr']);
		$resource = mysql_query('SELECT * FROM prepod WHERE id='.$id_prep);
		if (!$resource) {
			echo "<p><b> Извините, ошибка на стороне сервера. Зайдите позже.</b><p>";
			echo "</div>";
			exit;
		} else {
			$prep = mysql_fetch_assoc($resource);
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
	<p><span class="name"><?php echo $prep['name']?> </span></p>
	<span class="subtit"> Ученая степень и должность: </span> <?php echo $prep['post']?><br>
	<span class="subtit"> Дисциплины:</span> <?php echo $prep['predmet']?><br>
	<span class="subtit"> Область научных интересов:</span> <?php echo $prep['inter']?><br>
	<span class="subtit"> Контакты:</span> <?php echo $prep['contact']?><br>
</div>	
