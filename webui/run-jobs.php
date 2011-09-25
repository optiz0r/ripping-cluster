<?php

define('HBC_File', 'run-jobs');

require_once '_inc.php';

require_once 'Net/Gearman/Client.php';


try {
    $main = RippingCluster_Main::instance();
    $config = $main->config();
    $log = $main->log();
    
    $client = new Net_Gearman_Client($config->get('rips.job_servers'));
    $set    = new Net_Gearman_Set();
    
    // Retrieve a list of Created jobs
    $jobs = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::CREATED);
    
    foreach ($jobs as $job) {
        // Enqueue the job using gearman
        $args = $job->queue();
        $task = new Net_Gearman_Task($args['method'], $args);
        $task->attachCallback('gearman_complete', Net_Gearman_Task::TASK_COMPLETE);
        $task->attachCallback('gearman_fail', Net_Gearman_Task::TASK_FAIL);
        $set->addTask($task);
        
        $job->updateStatus(RippingCluster_JobStatus::QUEUED);
        RippingCluster_ClientLogEntry::info($log, $args['rip_options']['id'], 'Job queued', 'client');
    }
    
    $job_count = count($jobs);
    RippingCluster_ClientLogEntry::info($log, null, "Job queue started with {$job_count} jobs.", 'batch');
    
    // Start the job queue
    $result = $client->runSet($set);
        
    RippingCluster_ClientLogEntry::info($log, null, 'Job queue completed', 'batch');
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception (" . get_class($e) . "): " . $e->getMessage() . "\n");
}


function gearman_complete($method, $handle, $result) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    $job = RippingCluster_Job::fromId($result['id']);
    $job->updateStatus(RippingCluster_JobStatus::COMPLETE);
    
    RippingCluster_ClientLogEntry::info($log, $job->id(), 'Job complete');
}

function gearman_fail($task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    $job = RippingCluster_Job::fromId($task->arg['rip_options']['id']);
    $job->updateStatus(RippingCluster_JobStatus::FAILED);
    
    RippingCluster_ClientLogEntry::info($log, $job->id(), "Job failed with message: {$task->result}");
}





?>
