<?php
include_once('include/site.php');
include_once('./lib/site.php');
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
<?php /*<img class = "rightimg" src = 'photo/shema1.jpg'> */ ?>
	<p class = "tit">Контакты</p>
	<p class = "subtit">Адрес:</p> 
	Московский Государственный Технический Университет Гражданской Авиации<br>
	Кафедра Прикладной Математики<br>
	Москва, улица Пулковская 6, корпус 4<br>
	<p class = "subtit">Телефоны:</p>
	Наделяева Людмила Михайловна, методист: (495) 458-84-16 <br>
	Кузнецов Валерий Леонидович, зав. кафедрой: (495) 458-84-16 <br>
</div>
</body>
</html>
