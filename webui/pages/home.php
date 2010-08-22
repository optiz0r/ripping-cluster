<?php

    $running_jobs   = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::RUNNING, 5);
    $completed_jobs = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::COMPLETE, 5);
    $failed_jobs    = HandBrakeCluster_Job::allWithStatus(HandBrakeCluster_JobStatus::FAILED, 5);

    $this->smarty->assign('running_jobs', $running_jobs);
    $this->smarty->assign('completed_jobs', $completed_jobs);
    $this->smarty->assign('failed_jobs', $failed_jobs);

?>
