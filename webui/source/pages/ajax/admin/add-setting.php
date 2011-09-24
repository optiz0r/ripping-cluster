<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

$messages = array();
$result = false;

try {
    $name   = $req->get('name', 'RippingCluster_Exception_InvalidParameters');
    $type   = $req->get('type', 'RippingCluster_Exception_InvalidParameters');
    
    // Convert the web-friendly type field into the correct internal name
    $value = null;
    switch($type) {
        case 'bool': {
            $type = Sihnon_Config::TYPE_BOOL;
            $value = false;
        } break;
        case 'int': {
            $type = Sihnon_Config::TYPE_INT;
            $value = 0;
        } break;
        case 'string': {
            $type = Sihnon_Config::TYPE_STRING;
            $value = '';
        } break;
        case 'string-list': {
            $type = Sihnon_Config::TYPE_STRING_LIST;
            $value = array();
        } break;
        case 'hash': {
            $type = Sihnon_Config::TYPE_HASH;
            $value = array();
        } break;
    }
    
    // Add the new (empty) value. This is because no suitable UI has been presented yet.
    // Possible future fix, to insert intermediate dialog to capture the value using the correct UI.
    $result = $config->add($name, $type, $value);
    $this->smarty->assign('success', $result);
    
    $this->smarty->assign('name', $name);
    $this->smarty->assign('id', str_replace('.', '-',$name));
    $this->smarty->assign('type', $type);
    $this->smarty->assign('value', '');

} catch(RippingCluster_Exception $e) {
    $messages[] = get_class($e) . ':' . $e->getMessage();
    $this->smarty->assign('messages', $messages);
    $this->smarty->assign('success', false);    
}

?>