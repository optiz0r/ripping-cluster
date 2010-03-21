<?php

require 'HandBrakeCluster/Main.class.php';
try {
    $main = HandBrakeCluster_Main::instance();
    $smarty = $main->smarty();
    
    $page = new HandBrakeCluster_Page($smarty, $main->request());
    $page->evaluate();
    
    $smarty->display('index.tpl');
} catch (HandBrakeCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>
