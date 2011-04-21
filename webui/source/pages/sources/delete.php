<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$encoded_filename = null;
if ($req->get('confirm')) {
    $plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
    $encoded_filename = $req->get('id', 'RippingCluster_Exception_InvalidParameters');
    
    $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $encoded_filename, false);
    $source->delete();
    
    // Redirect back to the sources page
    RippingCluster_Page::redirect('rips/sources');

} else {
    $plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
    $encoded_filename = $req->get('id', 'RippingCluster_Exception_InvalidParameters');
    
    $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $encoded_filename, false);
    
    $this->smarty->assign('source', $source);
}

?>