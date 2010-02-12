<?php
include_once('include/site.php');

function print_sidebar()
{
?>
<div id = "vmenu">
	<p>Навигация</p>
	<ul>	
		<li><a href="">Расписание занятий (+)</a></li>
			<ul id = "menulink">
				<li><a href = "<?php echo stud_url . "/schedule.php?course=1&group=1" ?>">Курс 1</a></li>
				<li><a href = "<?php echo stud_url . "/schedule.php?course=2&group=1" ?>">Курс 2</a></li>
				<li><a href = "<?php echo stud_url . "/schedule.php?course=3&group=1" ?>">Курс 3</a></li>
				<li><a href = "<?php echo stud_url . "/schedule.php?course=4&group=1" ?>">Курс 4</a></li>
				<li><a href = "<?php echo stud_url . "/schedule.php?course=5&group=1" ?>">Курс 5</a></li>
			</ul>
			<li><a href="<?php echo stud_url . "/disc.php";?>">Предметы</a></li>
		<li><a href="">Диплом</a></li>
		<li><a href="">Государственные экзамены</a></li>
	</ul>
</div>
<?php
}
?>
