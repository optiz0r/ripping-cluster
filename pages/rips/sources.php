<?php

$main = HandBrakeCluster_Main::instance();
$config = $main->config();

$lister = new HandBrakeCluster_Rips_SourceLister($config->get('rips.source_dir'));
$sources = $lister->sources();

$sources_cached = array();
foreach ($sources as $source) {
    $sources_cached[$source] = HandBrakeCluster_Rips_Source::isCached($source);
}

$this->smarty->assign('sources', $sources);
$this->smarty->assign('sources_cached', $sources_cached);

?>