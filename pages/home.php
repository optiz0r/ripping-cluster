<?php

    $running_jobs = array();
    $completed_jobs = array();

    $running_jobs[] = new HandBrakeCluster_Job();

    $this->smarty->assign('running_jobs', $running_jobs);
    $this->smarty->assign('completed_jobs;', $completed_jobs);

?>
