<?php

$main = RippingCluster_Main::instance();
$req  = $main->request();

$this->smarty->assign('requested_page', htmlspecialchars($req->request_string()));


?>