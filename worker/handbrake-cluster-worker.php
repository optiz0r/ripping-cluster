<?php

define('HBC_File', 'worker');

require_once '../config.php';
require_once RippingCluster_Lib . 'RippingCluster/Main.class.php';

try {
    
    set_time_limit(0);
    
    $main = RippingCluster_Main::instance();
    $smarty = $main->smarty();

    $worker = new RippingCluster_Worker();
    $worker->start();
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>