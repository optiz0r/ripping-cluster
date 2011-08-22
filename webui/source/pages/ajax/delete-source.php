<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$encoded_filename = null;
if ($req->exists('confirm')) {
    $this->smarty->assign('confirmed', true);
    
    $plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
    $encoded_filename = $req->get('id', 'RippingCluster_Exception_InvalidParameters');
    
    $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $encoded_filename, false);
    $source->delete();
    
    // Generate a new list of sources to update the page with
    $all_sources = RippingCluster_Source_PluginFactory::enumerateAll();
    $this->smarty->assign('all_sources', $all_sources);
    
} else {
    $this->smarty->assign('confirmed', false);
    
    $plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
    $encoded_filename = $req->get('id', 'RippingCluster_Exception_InvalidParameters');
    
    $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $encoded_filename, false);
    
    $this->smarty->assign('source', $source);
    $this->smarty->assign('source_plugin', $plugin);
    $this->smarty->assign('source_id', $encoded_filename);
}

?>