<?php

$main = HandBrakeCluster_Main::instance();
$config = $main->config();

$lister = new HandBrakeCluster_Rips_SourceLister($config->get('rips.source_dir'));

$this->smarty->assign('sources', $lister->sources());

?>