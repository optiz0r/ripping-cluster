<?php

define('HBC_File', 'run-jobs');

require_once '../private/config.php';
require_once(SihnonFramework_Lib . 'SihnonFramework/Main.class.php');
require_once 'Net/Gearman/Client.php';

SihnonFramework_Main::registerAutoloadClasses('Sihnon', SihnonFramework_Lib,
												'RippingCluster', SihnonFramework_Main::makeAbsolutePath('../lib/'));

try {
    $main = RippingCluster_Main::instance();
    $config = $main->config();
    $log = $main->log();
    
    $client = new Net_Gearman_Client('river.sihnon.net:4730');//$config->get('rips.job_servers'));
    $set    = new Net_Gearman_Set();
    
    // Retrieve a list of Created jobs
    $jobs = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::CREATED);
    
    foreach ($jobs as $job) {
        // Enqueue the job using gearman
        list($method, $rip_options) = $job->queue();
        $task = new Net_Gearman_Task($method, $rip_options);
        $task->attachCallback('gearman_complete', Net_Gearman_Task::TASK_COMPLETE);
        $task->attachCallback('gearman_fail', Net_Gearman_Task::TASK_FAIL);
        $set->addTask($task);
        
        $job->updateStatus(RippingCluster_JobStatus::QUEUED);
    }
    
    // Start the job queue
    $result = $client->runSet($set);
        
    $log->info("Job queue completed");
    
} catch (RippingCluster_Exception $e) {
    die("Uncaught Exception (" . get_class($e) . "): " . $e->getMessage() . "\n");
}


function gearman_complete($method, $handle, $result) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    /*$log->info("Job Complete", $job->id());*/
    $log->info("Job complete");
}

function gearman_fail($task) {
    $main = RippingCluster_Main::instance();
    $log = $main->log();
    
    /*$job = RippingCluster_Job::fromId($gearman_task->unique());
    $job->updateStatus(RippingCluster_JobStatus::FAILED);
    
    $log->info("Job Failed", $job->id());*/
    $log->info("Job failed");
}





?>
