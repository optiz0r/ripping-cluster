<?php

require 'HandBrakeCluster/Main.class.php';
try {
    $main = HandBrakeCluster_Main::instance();
    $smarty = $main->smarty();
    
    $page = new HandBrakeCluster_Page($smarty, $main->request());
    $page->evaluate();
    
    $smarty->assign('page_content', $smarty->fetch($page->template_filename()));
    
    $smarty->display('index.tpl');
} catch (HandBrakeCluster_Exception $e) {
    die("Uncaught Exception: " . $e->getMessage());
}

?>
