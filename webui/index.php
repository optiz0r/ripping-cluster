<?php

define('HBCFile', 'index');

require '_inc.php';

try {
    $main = HandBrakeCluster_Main::instance();
    $smarty = $main->smarty();
    
    $page = new HandBrakeCluster_Page($smarty, $main->request());
    if ($page->evaluate()) {
        $smarty->display('index.tpl');
    }
    
} catch (HandBrakeCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>
