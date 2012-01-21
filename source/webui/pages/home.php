<?php

    $running_jobs   = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::RUNNING, 10);
    $queued_jobs    = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::QUEUED, 10);
    $completed_jobs = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::COMPLETE, 10);
    $failed_jobs    = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::FAILED, 10);

    $this->smarty->assign('running_jobs', $running_jobs);
    $this->smarty->assign('queued_jobs', $queued_jobs);
    $this->smarty->assign('completed_jobs', $completed_jobs);
    $this->smarty->assign('failed_jobs', $failed_jobs);

?>
