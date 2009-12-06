<?php
function view($doc)
{
	$classes = $doc->getElementsByTagName('discpl');
	$k = 0; $discp = array();
	foreach ($classes as $class) {
		$titl = $classes->item($k)->getElementsByTagName('title');
		$prep = $classes->item($k)->getElementsByTagName('prepod');
		$cntr = $classes->item($k)->getElementsByTagName('control');
		$kurs = $classes->item($k)->getElementsByTagName('kurs');
		
		$discp[$k]['titl'] = $titl->item(0)->nodeValue;
		$discp[$k]['prep'] = $prep->item(0)->nodeValue;
		$discp[$k]['cntr'] = $cntr->item(0)->nodeValue;
		$discp[$k]['kurs'] = $kurs->item(0)->nodeValue;
		$k++;
	}
	return $discp;
}
?>

