<?php

define('HBC_File', 'run-jobs');

require_once '../config.php';
require_once HandBrakeCluster_Lib . 'HandBrakeCluster/Main.class.php';

try {
    $main = HandBrakeCluster_Main::instance();
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
    $jobs = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::CREATED);
    
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
    
} catch (HandBrakeCluster_Exception $e) {
    die("Uncaught Exception (" . get_class($e) . "): " . $e->getMessage() . "\n");
}


function gearman_created_callback($gearman_task) {
    $main = HandBrakeCluster_Main::instance();
    $log = $main->log();

    $job = HandBrakeCluster_Job::fromId($gearman_task->unique());
    $job->updateStatus(HandBrakeCluster_JobStatus::RUNNING);
    
    $log->info("Job successfully queued with Gearman", $gearman_task->unique());
}

function gearman_data_callback($gearman_task) {
    $main = HandBrakeCluster_Main::instance();
    $log = $main->log();
    
    $log->debug("Got some data from gearman", $gearman_task->unique());
}

function gearman_status_callback($gearman_task) {
    $main = HandBrakeCluster_Main::instance();
    $log = $main->log();

    $job = HandBrakeCluster_Job::fromId($gearman_task->unique());
    $status = $job->currentStatus();
    
    $rip_progress = $gearman_task->taskNumerator() / $gearman_task->taskDenominator();
    if ($rip_progress > $status->ripProgress() + 0.1) {
        $status->updateRipProgress($rip_progress);
    }
}

function gearman_complete_callback($gearman_task) {
    $main = HandBrakeCluster_Main::instance();
    $log = $main->log();
    
    $job = HandBrakeCluster_Job::fromId($gearman_task->unique());
    $job->updateStatus(HandBrakeCluster_JobStatus::COMPLETE);
    
    $log->info("Job Complete", $job->id());
}

function gearman_fail_callback($gearman_task) {
    $main = HandBrakeCluster_Main::instance();
    $log = $main->log();
    
    $job = HandBrakeCluster_Job::fromId($gearman_task->unique());
    $job->updateStatus(HandBrakeCluster_JobStatus::FAILED);
    
    $log->info("Job Failed", $job->id());
}





?>
