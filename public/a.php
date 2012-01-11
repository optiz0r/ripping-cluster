<?php

define('HBC_File', 'ajax');

require '_inc.php';

try {
    $main = RippingCluster_Main::instance();
    RippingCluster_LogEntry::setLocalProgname('webui');        	
    $smarty = $main->smarty();
    
    $page = new RippingCluster_Page($smarty, $main->request());
    if ($page->evaluate()) {
        //header('Content-Type: text/json');
        $smarty->display('ajax.tpl');
    }
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>
