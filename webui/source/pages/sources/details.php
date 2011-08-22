<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
$source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $req->get('id', 'RippingCluster_Exception_InvalidParameters'));

$this->smarty->assign('source', $source);
$this->smarty->assign('titles', $source->titles());

?>