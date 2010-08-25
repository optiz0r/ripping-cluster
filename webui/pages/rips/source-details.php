<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$source = RippingCluster_Rips_Source::loadEncoded($req->get('id', RippingCluster_Exception_InvalidParameters));

$this->smarty->assign('source', $source);
$this->smarty->assign('titles', $source->titles());

?>