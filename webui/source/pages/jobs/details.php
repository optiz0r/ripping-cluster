<?php

$job_id = $this->request->get('id');
$job = RippingCluster_Job::fromId($job_id);
$this->smarty->assign('job', $job);

$log = RippingCluster_Main::instance()->log();

$client_log_entries = RippingCluster_LogEntry::recentEntriesByField($log, 'webui', 'job_id', $job_id, 'ctime', SihnonFramework_Log::ORDER_DESC, 30);
$worker_log_entries = RippingCluster_LogEntry::recentEntriesByField($log, 'worker', 'job_id', $job_id, 'ctime', SihnonFramework_Log::ORDER_DESC, 30);
$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);


?>
