<?php

$main   = HandBrakeCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$source_id = $req->get('id');
$source_path = base64_decode(str_replace('-', '/', $source_id));
$real_source_path = realpath($source_path);

// Ensure the source is a valid directory, and lies below the configured source_dir
if (!is_dir($source_path)) {
    return;
}
$real_source_dir = realpath($config->get('rips.source_dir'));
if (substr($real_source_path, 0, strlen($real_source_dir)) != $real_source_dir) {
    return;
}

$source = new HandBrakeCluster_Rips_Source($source_path);

$this->smarty->assign('source_path', $source_path);
$this->smarty->assign('source', $source);
$this->smarty->assign('output', $source->output());

?>