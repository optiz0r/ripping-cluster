<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$messages = array();
$result = false;

$confirm = $req->exists('confirm');
$this->smarty->assign('confirm', $confirm);

if ($confirm) {
    try {
        $name     = $req->get('name', 'RippingCluster_Exception_InvalidParameters');
        $new_name = $req->get('new-name', 'RippingCluster_Exception_InvalidParameters');
        
        $result = $config->rename($name, $new_name);
        $this->smarty->assign('success', $result);
        
        $this->smarty->assign('old_name', $name);
        $this->smarty->assign('old_id', str_replace('.', '-', $name));
        $this->smarty->assign('name', $new_name);
        $this->smarty->assign('id', str_replace('.', '-', $new_name));
        $this->smarty->assign('type', $config->type($new_name));
        $this->smarty->assign('value', $config->get($new_name));
        
    } catch(RippingCluster_Exception $e) {
        $messages[] = get_class($e) . ':' . $e->getMessage();
        $this->smarty->assign('messages', $messages);
        $this->smarty->assign('success', false);    
    }
} else {
    $name     = $req->get('name', 'RippingCluster_Exception_InvalidParameters');
    $this->smarty->assign('name', $name);
}
    

?>