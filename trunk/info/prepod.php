<?php
	include_once('db.class.php');
	require_once('include/logins.php');
	
	$db = new Db_Mysql();

	//!!! вставьте свой логин и пароль
	$link = mysql_connect('localhost', dbuser,dbpassword) or die("Could not connect: ".mysql_error());
	mysql_select_db('clericsu_kafedrapm',$link);

	if (isset($_GET['id_pr']))
	{
		$id_prep = mysql_escape_string($_GET['id_pr']);
		$prep = $db->fetchRow('SELECT * FROM prepod WHERE id='.$id_prep);
	}
?>

<?php include_once("info/sidebar.php"); ?>

<div id = "main"> 
	<?php $photo = "info/photo/".$prep['id'].".jpg"; ?>
	<img  src="<?php echo $photo; ?>" class = "prep_photo">
	<p><span class="name"><?php echo $prep['name']?> </span></p>
	<span class="subtit"> Ученая степень и должность: </span> <?php echo $prep['post']?><br>
	<span class="subtit"> Дисциплины:</span> <?php echo $prep['predmet']?><br>
	<span class="subtit"> Область научных интересов:</span> <?php echo $prep['inter']?><br>
	<span class="subtit"> Контакты:</span> <?php echo $prep['contact']?><br>
</div>	
