<?php

require 'HandBrakeCluster/Main.class.php';
$main = HandBrakeCluster_Main::instance();
$smarty = $main->smarty();

$page = new HandBrakeCluster_Page($smarty, $main->request());
$page->evaluate();

$smarty->assign('page_content', $smarty->fetch($page->template_filename()));

$smarty->display('index.tpl');

?>
