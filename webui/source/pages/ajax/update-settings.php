<?php

$main = RippingCluster_Main::instance();
$config = $main->config();

// Iterate over the settings and store each one back to the backend
foreach($_POST as $key => $value) {
    if ($config->exists($key)) {
        $config->set($key, $value);
    }
}

$this->smarty->assign('messages', $messages);

?>