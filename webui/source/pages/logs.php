<?php

$client_log_entries = RippingCluster_ClientLogEntry::recent(30);
$worker_log_entries = RippingCluster_WorkerLogEntry::recent(30);

$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);

?>
