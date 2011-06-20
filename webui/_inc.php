<?php

require_once '/etc/ripping-cluster/config.php';
require_once RippingCluster_Lib . 'RippingCluster/Main.class.php';

RippingCluster_Main::registerAutoloadClasses('SihnonFramework', SihnonFramework_Lib,
												'RippingCluster', RippingCluster_Lib);

?>
