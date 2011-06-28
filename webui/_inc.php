<?php

require_once '/etc/ripping-cluster/config.php';
require_once SihnonFramework_Lib . 'SihnonFramework/Main.class.php';

SihnonFramework_Main::registerAutoloadClasses('SihnonFramework', SihnonFramework_Lib,
												'RippingCluster', RippingCluster_Lib);

?>
