<?php

    $running_jobs   = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::RUNNING, 5);
    $completed_jobs = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::COMPLETE, 5);
    $failed_jobs    = RippingCluster_Job::allWithStatus(RippingCluster_JobStatus::FAILED, 5);

    $this->smarty->assign('running_jobs', $running_jobs);
    $this->smarty->assign('completed_jobs', $completed_jobs);
    $this->smarty->assign('failed_jobs', $failed_jobs);

?>
