<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$encoded_filename = null;
if ($req->get('submit')) {
    $encoded_filename = RippingCluster_Main::issetelse($_POST['id'], 'RippingCluster_Exception_InvalidParameters');

    // Create the jobs from the request
    $jobs = RippingCluster_Job::fromPostRequest($_POST['plugin'], $_POST['id'], $_POST['rip-options'], $_POST['rips']);
    
    // Spawn the background client process to run all the jobs
    RippingCluster_Job::runAllJobs();
    
    RippingCluster_Page::redirect('rips/setup-rip/queued');
    
} elseif ($req->get('queued')) {
    $this->smarty->assign('rips_submitted', true);
    
} else {
    $this->smarty->assign('rips_submitted', false);
    $encoded_filename = $req->get('id', 'RippingCluster_Exception_InvalidParameters');
    
    $plugin = $req->get('plugin', 'RippingCluster_Exception_InvalidParameters');
    $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, $encoded_filename);
    
    $this->smarty->assign('source', $source);
    $this->smarty->assign('titles', $source->titles());
    $this->smarty->assign('longest_title', $source->longestTitle());
    $this->smarty->assign('default_output_directory', $config->get('rips.default.output_directory'));
}

?>
