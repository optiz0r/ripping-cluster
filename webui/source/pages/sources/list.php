<?php

$main = RippingCluster_Main::instance();
$config = $main->config();

$all_sources = RippingCluster_Source_PluginFactory::enumerateAll();
$this->smarty->assign('all_sources', $all_sources);

?>