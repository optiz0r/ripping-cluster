<?php

$main = RippingCluster_Main::instance();
$config = $main->config();

$messages = array();

// Iterate over the settings and store each one back to the backend
foreach($_POST as $key => $value) {
    // Convert - to . (to work around the PHP register global backwards compatibility that renames input variables)
    $key = str_replace("-", ".", $key);
    
    if ($config->exists($key)) {
        $config->set($key, $value);
    } else {
        $messages[] = "Unknown config key '{$key}', value not updated.";
    }
}

$this->smarty->assign('messages', $messages);

?>