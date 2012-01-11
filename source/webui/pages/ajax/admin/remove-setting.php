<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$messages = array();
$result = false;

try {
    $name   = $req->get('name', 'RippingCluster_Exception_InvalidParameters');

    $result = $config->remove($name);
    $this->smarty->assign('success', $result);
    
} catch(RippingCluster_Exception $e) {
    $messages[] = get_class($e) . ':' . $e->getMessage();
    $this->smarty->assign('messages', $messages);
    $this->smarty->assign('success', false);    
}
    

?>