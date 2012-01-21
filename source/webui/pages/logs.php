<?php

$log = RippingCluster_Main::instance()->log();

$client_log_entries = RippingCluster_LogEntry::recentEntries($log, 'webui', 'ctime', SihnonFramework_Log::ORDER_DESC, 30);
$worker_log_entries = RippingCluster_LogEntry::recentEntries($log, 'worker', 'ctime', SihnonFramework_Log::ORDER_DESC, 30);

$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);

?>
