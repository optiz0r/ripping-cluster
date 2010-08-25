<?php

define('HBC_File', 'index');

require '_inc.php';

try {
    $main = RippingCluster_Main::instance();
    $smarty = $main->smarty();
    
    $page = new RippingCluster_Page($smarty, $main->request());
    if ($page->evaluate()) {
        $smarty->display('index.tpl');
    }
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>
