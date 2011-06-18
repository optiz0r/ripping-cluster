<?php

define('HBC_File', 'worker');

require_once '/etc/ripping-cluster/config.php';
require_once(SihnonFramework_Lib . 'SihnonFramework/Main.class.php');
require_once 'Net/Gearman/Worker.php';

SihnonFramework_Main::registerAutoloadClasses('SihnonFramework', SihnonFramework_Lib,
												'RippingCluster', SihnonFramework_Main::makeAbsolutePath('../source/lib/'));
SihnonFramework_Main::registerAutoloadClasses('Net', SihnonFramework_Main::makeAbsolutePath('../source/lib/'));


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
