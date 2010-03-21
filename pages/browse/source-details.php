<?php

$main   = HandBrakeCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

// Grab the name of this source
$source_id = $req->get('id');
$source = base64_decode(str_replace('-', '/', $source_id));
$real_source = realpath($source);

// Ensure the source is a valid directory, and lies below the configured source_dir
if (!is_dir($source)) {
    return;
}

$real_source_dir = realpath($config->get('rips.source_dir'));
if (substr($real_source, 0, strlen($real_source_dir)) != $real_source_dir) {
    return;
}

$this->smarty->assign('source', $source);

?>