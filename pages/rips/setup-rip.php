<?php

$main   = HandBrakeCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$source_id;
if ($req->get('submit')) {
    $this->smarty->assign('rips_submitted', true);
    $source_id = HandBrakeCluster_Main::issetelse($_POST['id'], HandBrakeCluster_Exception_InvalidParameters);
    
    $this->smarty->assign('rips', HandBrakeCluster_Main::issetelse($_POST['rips'], HandBrakeCluster_Exception_InvalidParameters));
} else {
    $this->smarty->assign('rips_submitted', false);
    $source_id = $req->get('id', HandBrakeCluster_Exception_InvalidParameters);
}

$source_path = base64_decode(str_replace('-', '/', $source_id));
$real_source_path = realpath($source_path);

// Ensure the source is a valid directory, and lies below the configured source_dir
if (!is_dir($source_path)) {
    throw new HandBrakeCluster_Exception_InvalidParameters();
}

$real_source_dir = realpath($config->get('rips.source_dir'));
if (substr($real_source_path, 0, strlen($real_source_dir)) != $real_source_dir) {
    throw new HandBrakeCluster_Exception_InvalidParameters();
}

$source = HandBrakeCluster_Rips_Source::load($source_path);

$this->smarty->assign('source_path_encoded', $source_id);
$this->smarty->assign('source_path', $source_path);
$this->smarty->assign('source', $source);
$this->smarty->assign('titles', $source->titles());
$this->smarty->assign('longest_title', $source->longestTitle());

?>