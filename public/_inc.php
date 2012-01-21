<?php

if (isset($_SERVER['RIPPING_CLUSTER_CONFIG']) && 
    file_exists($_SERVER['RIPPING_CLUSTER_CONFIG']) &&
    is_readable($_SERVER['RIPPING_CLUSTER_CONFIG'])) {
    require_once($_SERVER['RIPPING_CLUSTER_CONFIG']);
} else {
    require_once '/etc/ripping-cluster/config.php';
}

require_once SihnonFramework_Lib . 'SihnonFramework/Main.class.php';

SihnonFramework_Main::registerAutoloadClasses('SihnonFramework', SihnonFramework_Lib,
												'RippingCluster', RippingCluster_Lib);

?>
