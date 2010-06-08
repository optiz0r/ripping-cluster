<?php

$main   = HandBrakeCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$encoded_filename = null;
if ($req->get('submit')) {
    $encoded_filename = HandBrakeCluster_Main::issetelse($_POST['id'], HandBrakeCluster_Exception_InvalidParameters);

    // Create the jobs from the request
    $jobs = HandBrakeCluster_Job::fromPostRequest($_POST['id'], $_POST['rip-options'], $_POST['rips']);
    
    // Spawn the background client process to run all the jobs
    HandBrakeCluster_Job::runAllJobs();
    
    HandBrakeCluster_Page::redirect('rips/setup-rip/queued');
    
} elseif ($req->get('queued')) {
    $this->smarty->assign('rips_submitted', true);
    
} else {
    $this->smarty->assign('rips_submitted', false);
    $encoded_filename = $req->get('id', HandBrakeCluster_Exception_InvalidParameters);

    $source = HandBrakeCluster_Rips_Source::loadEncoded($encoded_filename);
    
    $this->smarty->assign('source', $source);
    $this->smarty->assign('titles', $source->titles());
    $this->smarty->assign('longest_title', $source->longestTitle());
}

?>
