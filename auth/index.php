<?php

/**
 * @author nameless
 * @copyright 2009
 */
    
    require_once('QuickTemplate.php');
        
    $temp_data = array(
        'main' => array('file' => 'index.thtml'),
        'additional' => array('content' => 'бла'),
        'content' => array('content' => 'бла-бла!')
    );
    
    $engine = new QuickTemplate($temp_data);
    echo $engine->parse();


?>