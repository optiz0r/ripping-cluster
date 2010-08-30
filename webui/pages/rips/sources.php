<?php

$main = RippingCluster_Main::instance();
$config = $main->config();

$sources = RippingCluster_Source_PluginFactory::enumerateAll();

$sources_cached = array();
foreach ($sources as $source) {
    $sources_cached[$source->filename()] = RippingCluster_Source::isCached($source->filename());
}

$this->smarty->assign('sources', $sources);
$this->smarty->assign('sources_cached', $sources_cached);

?>