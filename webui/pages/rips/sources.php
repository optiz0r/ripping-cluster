<?php

$main = RippingCluster_Main::instance();
$config = $main->config();

$lister = new RippingCluster_Rips_SourceLister($config->get('rips.source_dir'));
$sources = $lister->sources();

$sources_cached = array();
foreach ($sources as $source) {
    $sources_cached[$source->filename()] = RippingCluster_Rips_Source::isCached($source->filename());
}

$this->smarty->assign('sources', $sources);
$this->smarty->assign('sources_cached', $sources_cached);

?>