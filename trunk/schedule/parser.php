<?php
	 define("XML_FILE",'temp.xml');

    $dom = new DomDocument;
    $dom->preserveWhiteSpace = false;
    $dom->load(XML_FILE);
       
    $domxpath = new domxpath($dom);
    $domxpath->registerNamespace("m","urn:schemas-microsoft-com:office:spreadsheet");
    $query = "/m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce12']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce11']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce13']/m:Data | /m:Workbook/m:Worksheet/m:Table/m:Row/m:Cell[@m:StyleID='ce3']/m:Data";
    
    $datas = $domxpath->query($query);
    $filename = "result.txt";
    $filename2 = "2.txt";
	if(is_writeable($filename) && is_file($filename2)){
		$fl = fopen($filename, "a+");
		$fl_array = file($filename2);
		$j = $k = 0;
    	for($i = 0; $i < $datas->length; $i++){
    		$dt = trim($datas->item($i)->nodeValue);
    		$fl_array[$j] = trim($fl_array[$j],"\n");
    		$fl_array[$j] .= $dt.":";$k++;
    		if($k % 4 === 0){
    			$fl_array[$j] .= "\n";
    			fwrite($fl,$fl_array[$j]);
    			$j++;
    		}
    		
    	}

    	fclose($fl);
    }else echo "Проверьте существование файла 2.txt \n";
 
?>