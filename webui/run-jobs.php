<?php

define('HBC_File', 'run-jobs');

require_once '../config.php';
require_once RippingCluster_Lib . 'RippingCluster/Main.class.php';

try {
    $main = RippingCluster_Main::instance();
    $config = $main->config();
    $log = $main->log();
    
    $gearman = new GearmanClient();
    $gearman->addServers($config->get('rips.job_servers'));
    $gearman->setCreatedCallback("gearman_created_callback");
    $gearman->setDataCallback("gearman_data_callback");
    $gearman->setStatusCallback("gearman_status_callback");
    $gearman->setCompleteCallback("gearman_complete_callback");
    $gearman->setFailCallback("gearman_fail_callback");
    
    // Retrieve a list of Created jobs
    $jobs = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::CREATED);
    
    foreach ($jobs as $job) {
        // Enqueue the job using gearman
        $job->queue($gearman);
    }
    
    // Start the job queue
    $result = $gearman->runTasks();
    if (!$result) {
        $log->error($gearman->error());
        die($gearman->error());
    }
    
    $log->info("Job queue completed");
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception (" . get_class($e) . "): " . $e->getMessage() . "\n");
}


function gearman_created_callback($gearman_task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();

    $log->info("Job successfully queued with Gearman", $gearman_task->unique());
}

function gearman_data_callback($gearman_task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    $log->debug("Received data callback from Gearman Task");
}

function gearman_complete_callback($gearman_task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    $log->info("Job Complete", $job->id());
}

function gearman_fail_callback($gearman_task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    $job = RippingCluster_Job::fromId($gearman_task->unique());
    $job->updateStatus(RippingCluster_JobStatus::FAILED);
    
    $log->info("Job Failed", $job->id());
}





?>
