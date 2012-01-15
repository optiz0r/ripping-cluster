<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$encoded_filename = null;
if ($req->exists('submit')) {
    $encoded_filename = RippingCluster_Main::issetelse($_POST['id'], 'RippingCluster_Exception_InvalidParameters');
    
    // Update the recently used list
    $recent_output_directories = $config->get('rips.output_directories.recent');
    if (( $key = array_search($_POST['rip-options']['output-directory'], $recent_output_directories, true))) {
        // Move the entry to the top of the recently used list if necessary
        $recent_directory = array_splice($recent_output_directories, $key, 1);
        if ($key > 0) {
            array_unshift($recent_output_directories, $recent_directory[0]);
            $config->set('rips.output_directories.recent', array_slice($recent_output_directories, 0, $config->get('rips.output_directories.recent_limit', 10)));
        }
    } else {
        array_unshift($recent_output_directories, $_POST['rip-options']['output-directory']);
        $config->set('rips.output_directories.recent', array_slice($recent_output_directories, 0, $config->get('rips.output_directories.recent_limit', 10)));
    }

    // Create the jobs from the request
    $jobs = RippingCluster_Job::fromPostRequest($_POST['plugin'], $_POST['id'], $_POST['rip-options'], $_POST['rips']);
    
    // Spawn the background client process to run all the jobs
    RippingCluster_Job::runAllJobs();
    
    RippingCluster_Page::redirect('rips/setup/queued');
    
} elseif ($req->exists('queued')) {
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
    
    $default_output_directories = $config->get('rips.output_directories.default');
    $recent_output_directories  = $config->get('rips.output_directories.recent');
    $this->smarty->assign('default_output_directories', $default_output_directories);
    $this->smarty->assign('recent_output_directories', $recent_output_directories);
    $this->smarty->assign('next_output_directory_index', count($default_output_directories) + count($recent_output_directories) + 1);
}

?>
