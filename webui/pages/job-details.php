<?php

$job_id = $this->request->get('id');
$job = RippingCluster_Job::fromId($job_id);
$this->smarty->assign('job', $job);

$client_log_entries = RippingCluster_ClientLogEntry::recentForJob($job_id, 30);
$worker_log_entries = RippingCluster_WorkerLogEntry::recentForJob($job_id, 30);
$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);


?>
