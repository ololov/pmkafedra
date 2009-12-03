<?php
	//Обьявление констант XML_FILE & TXT_FILE
	require_once('style_id.php');

	define("XML_FILE",'temp.xml');
	define("TXT_FILE",'para_plus_predmet.txt');
	//
    	$dom = new DomDocument;
	$dom->preserveWhiteSpace = false;
	$dom->load(XML_FILE);
       
	
	$domxpath = new domxpath($dom);
	$domxpath->registerNamespace("m","urn:schemas-microsoft-com:office:spreadsheet");
	/**
	* Формирование запроса для поиска по xml файлу,результатом будет 
	* тип(лекция, практика и тп), преподватель, аудитория, дата
	*/
	$query = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='".prepod."']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='".type."']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='".auditoriya."']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='".data."']/m:Data";

//	$query = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce12']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce11']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce13']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce3']/m:Data";

    	$datas = $domxpath->query($query);

	/**
	* Формирование строки вида 
	* <номер пары>:<предмет>:<преподаватель>:<аудитория>:<дата>
	*/

	if(is_file(TXT_FILE)){
		$fl_array = file(TXT_FILE);
		for($i = $j = $k = 0; $i < $datas->length; $i++){
			$dt = trim($datas->item($i)->nodeValue);
			$fl_array[$j] = trim($fl_array[$j],"\n").":".$dt;$k++;
			if($k % 4 === 0){
				echo $fl_array[$j]."\n";
				$j++;
			}
		}
	}else echo "Проверьте существование файла ".TXT_FILE."\n";
?>
