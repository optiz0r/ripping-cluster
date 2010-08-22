<?php

define('HBC_File', 'worker');

require_once '../config.php';
require_once HandBrakeCluster_Lib . 'HandBrakeCluster/Main.class.php';

try {
    
    set_time_limit(0);
    
    $main = HandBrakeCluster_Main::instance();
    $smarty = $main->smarty();

    $worker = new HandBrakeCluster_Worker();
    $worker->start();
    
} catch (HandBrakeCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>