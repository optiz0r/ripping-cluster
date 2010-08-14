<?php

$main   = HandBrakeCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$source = HandBrakeCluster_Rips_Source::loadEncoded($req->get('id', HandBrakeCluster_Exception_InvalidParameters));

$this->smarty->assign('source', $source);
$this->smarty->assign('titles', $source->titles());

?>