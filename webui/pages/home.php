<?php

    $running_jobs   = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::RUNNING);
    $completed_jobs = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::COMPLETE);
    $failed_jobs    = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::FAILED);

    $this->smarty->assign('running_jobs', $running_jobs);
    $this->smarty->assign('completed_jobs', $completed_jobs);
    $this->smarty->assign('failed_jobs', $failed_jobs);

?>
