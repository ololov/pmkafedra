<?php
	/**
	*Это скрипт еще не парсер, он тольок подготавливает исходный 
	*xml документ к виду пригодному для парсинга =) 
	*/
    define("XML_FILE",'1234.xml');

    $dom = new DomDocument;
    $dom->preserveWhiteSpace = false;
    $dom->load(XML_FILE);
       
    $domxpath = new domxpath($dom);
    $domxpath->registerNamespace("m","urn:schemas-microsoft-com:office:spreadsheet");
           
    $query = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce12']/m:Data";
    $query2 = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce2']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce10']/m:Data";
    
    $str_parse = "только";
    $filename = "2.txt";
    $datas = $domxpath->query($query);
    $datas2 = $domxpath->query($query2);
    
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

    if(is_writeable($filename)){
    	$fl = fopen($filename, "a+");
    	foreach($datas2 as $data){
    		$dt = $data->nodeValue;
  			if(is_numeric($dt)){
  				$para = $dt.":";
  				continue;
  			}
  			if(is_string($dt)){
  				if(isset($para)){
  					$str = $para.$dt.":\n";
  				}
  				fwrite($fl,$str);
  			}	  	
    	}
    	fclose($fl);
    }else echo "Файл $filename не создан \n";   
    echo $dom->saveXML();
?> 