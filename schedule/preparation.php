<?php
	/**
	* Это скрипт еще не парсер, он тольок подготавливает исходный 
	* xml документ к виду пригодному для парсинга =) 
	* Кто видел исходный xml документ,наверняка заметил, что не везде есть отдельный тег
	* для даты, в которую должен идти тот или иной предмет. Например:
	* <Data ss:Type="String">Ивенин И.Б., доц., только 01.09,08.09</Data>
	* Поэтому надо разделить этот тег на два тега, для этой цели и написан этот файл
	* =))
	* 
	*/
	define("XML_FILE",'1234.xml');
	define("TXT_FILE",'para_plus_predmet.txt');

	$dom = new DomDocument;
	$dom->preserveWhiteSpace = false;
	$dom->load(XML_FILE);
       
	$domxpath = new domxpath($dom);
	$domxpath->registerNamespace("m","urn:schemas-microsoft-com:office:spreadsheet");
        /**
	* Формирование двух запросов, первый ищет сторки с ФИО преподавателя,
	* второй ищет пары + название предмета
	*/
	$query = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce12']/m:Data";
	$query2 = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce2']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce10']/m:Data";
    
    	$str_parse = "только";
    	$datas = $domxpath->query($query);
	$datas2 = $domxpath->query($query2);
    
    	/**
	* Здесь происходит добавление в xml документ отдельного тега для
	* даты
	*/
	foreach($datas as $data){
    		if(stripos($data->nodeValue,$str_parse)){
			list($str1, $str2) = explode($str_parse,$data->nodeValue);
			$str1[strlen($str1)-2] = "";
			$str2 = $str_parse.$str2;
				
			$parent = $data->parentNode->parentNode;
						
			$new_cell = $dom->createElement('Cell');
			$new_cell->setAttribute("ss:StyleID","ce3");
			
			$new_data = $dom->createElement('Data',"$str2");
			$new_data->setAttribute("ss:Type","String");
			$new_cell->appendChild($new_data);
			
			$parent->appendChild($new_cell);
			$data->nodeValue = $str1;
			
			$dom->formatOutput = true;
    		}
    	}
	/**
	* Формирование сторки вида <номер пары>:<предмет>
	* и запись этой строки в файл
	*/
   	$fl = fopen(TXT_FILE, "w+");
	foreach($datas2 as $data){
    		$dt = $data->nodeValue;
  		if(is_numeric($dt)){
  			$para = $dt.":";
  			continue;
  		}
  		if(is_string($dt)){
  			if(isset($para)) $str = $para.$dt."\n";
  			fwrite($fl,$str);
			/**
			* Присвоение предмету Физ-ра преподавателся "Неизвестен" =))
			* 
			*/
			if($dt === "Физическая культура"){
				$parent = $data->parentNode->parentNode->nextSibling->firstChild;
						
				$new_data = $dom->createElement('Data',"Неизвестен");
				$new_data->setAttribute("ss:Type","String");
				$parent->appendChild($new_data);
			
				$dom->formatOutput = true;
			}

  		}	  	
    	}
    	fclose($fl);

    	echo $dom->saveXML();
?> 
