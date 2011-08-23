<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$log    = $main->log();
$config = $main->config();

$job_id = $req->get('id');
$job = RippingCluster_Job::fromId($job_id);
$this->smarty->assign('job', $job);

// Fetch log entries for this job
$log_count = $req->get('logs', $config->get('job.logs.default_display_count'));

$default_log_order = $config->get('job.logs.default_order');
$log_order = $req->get('order', $default_log_order);
if ( ! in_array($log_order, array(SihnonFramework_Log::ORDER_ASC, SihnonFramework_Log::ORDER_DESC))) {
    $log_order = $default_log_order;    
}
$this->smarty->assign('log_order', $log_order);
$this->smarty->assign('log_order_reverse', ($log_order == SihnonFramework_Log::ORDER_ASC ? SihnonFramework_Log::ORDER_DESC : SihnonFramework_Log::ORDER_ASC));

$client_log_entries = array();
$worker_log_entries = array();

$log_count_display = null;
if ($log_count == 'all') {
    $log_count_display = 'all';
    $log_count = '18446744073709551615'; // see mysql man page for LIMIT
} else if(!is_int($log_count)) {
    $log_count = $config->get('job.logs.default_display_count');
    $log_count_display = $log_count;
} else {
    $log_count_display = $log_count;
}

$client_log_entries = RippingCluster_LogEntry::recentEntriesByField($log, 'webui', 'job_id', $job_id, 'ctime', $log_order, $log_count);
$worker_log_entries = RippingCluster_LogEntry::recentEntriesByField($log, 'worker', 'job_id', $job_id, 'ctime', $log_order, $log_count);

$this->smarty->assign('log_count_display', $log_count_display);
$this->smarty->assign('client_log_entries', $client_log_entries);
$this->smarty->assign('worker_log_entries', $worker_log_entries);


?>
