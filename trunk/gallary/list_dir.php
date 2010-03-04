<?php

function my_in_array($pattern,$array){
	foreach($array as $val){
		if(fnmatch($val,$pattern) === true){
			return true;
		}
	}
	return false;
	
}
function my_count($array){
	$count = 0;
	foreach($array as $key=>$val){
		if(is_numeric($key)){
			$count++;
		}
	}
	return $count;
}

function my_print($array){
	if(is_array($array)){
		for($i = 0; $i < my_count($array); $i++){
			if(isset($array[$array[$i]]) && is_array($array[$array[$i]])){
				echo "<li><a href=\"#\">$array[$i]</a> <ul>\n";
				my_print($array[$array[$i]]);
			}else{
				echo "<li><a href=\"#\">$array[$i]</a></li>\n";
			}
		}
		echo "</li></ul>\n";
	}
}
/*
 * Функция выводит массив каталогов, вложенных пока до бесконечности =))
 *
 * $path - дирректория с которой начинать сканировать
 * $exclude - исключить файлы или каталоги удовлетворяющии шаблону, 
 * 	      шаблоны разделябются "|"
 * $recursive - рекурсивно пройти по каталогу или нет
 */
function dir_array($path = "data", $exclude = ".|..|.*", $recursive = true){
	$path = rtrim($path, "/") . "/";
        $folder_handle = opendir($path);
        $exclude_array = explode("|", $exclude);
        $result = array();
        while(false !== ($filename = readdir($folder_handle))) {
            if(!my_in_array(strtolower($filename), $exclude_array)) {
		if(is_dir($path . $filename . "/")){
			$result[] = $filename;
			if($recursive){
				$array = dir_array($path . $filename . "/", $exclude, true);
				if(!empty($array)){
					$result[$filename] = $array;
				}
			}
		}
                
            }

        }
        return $result;
}
?>
