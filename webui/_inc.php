<?php

require_once '../private/config.php';
require_once(SihnonFramework_Lib . 'SihnonFramework/Main.class.php');
//require_once RippingCluster_Lib . 'RippingCluster/Main.class.php';

SihnonFramework_Main::registerAutoloadClasses('Sihnon', SihnonFramework_Lib,
												'RippingCluster', SihnonFramework_Main::makeAbsolutePath('../source/lib/'));

?>
