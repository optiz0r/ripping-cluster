<?php

$log = RippingCluster_Main::instance()->log();

$client_log_entries = RippingCluster_LogEntry::recentEntries($log, 'webui', 30);
$worker_log_entries = RippingCluster_LogEntry::recentEntries($log, 'worker', 30);

$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);

?>
