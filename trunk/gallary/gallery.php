<!DOCTYPE html>
<html>
<?php
include_once('include/site.php');
include_once('./lib/site.php');
print_head("Главная страница");
?>

<body>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.lightbox-0.5.pack.js"></script>
<script type="text/javascript" language="javascript">
 $(document).ready(function(){
	
	$('.pic a').lightBox({
		
		imageLoading: '../images/loading.gif',
		imageBtnClose: '../images/close.gif',
		imageBtnPrev: '../images/prev.gif',
		imageBtnNext: '../images/next.gif'

	});
	
});
</script>
<?php
print_header();
print_sidebar();
?>
<div id = "<?php echo css_content_div; ?>">

<div id="gallery">

<?php

$directory = 'gallery';

$allowed_types=array('jpg','jpeg','gif','png');
$file_parts=array();
$ext='';
$title='';
$i=0;

$dir_handle = @opendir($directory) or die("There is an error with your image directory!");

while ($file = readdir($dir_handle)) 
{
	if($file=='.' || $file == '..') continue;
	
	$file_parts = explode('.',$file);
	$ext = strtolower(array_pop($file_parts));

	$title = implode('.',$file_parts);
	$title = htmlspecialchars($title);
	
	$nomargin='';
	
	if(in_array($ext,$allowed_types))
	{
		if(($i+1)%4==0) $nomargin='nomargin';
	
		echo '
		<div class="pic '.$nomargin.'" style="background:url('.$directory.'/'.$file.') no-repeat 50% 50%;">
		<a href="'.$directory.'/'.$file.'" title="'.$title.'" target="_blank">'.$title.'</a>
		</div>';
		
		$i++;
	}
}

closedir($dir_handle);

?>
<div class="clear"></div>
</div>

</div>

</body>
</html>
