<?php

$main   = RippingCluster_Main::instance();
$config = $main->config();

$settings = $config->enumerateAll();
$this->smarty->assign('settings', $settings);
$this->smarty->assign('config', $config);

?>