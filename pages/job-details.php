<?php

$job_id = $this->request->get('id');
$job = new HandBrakeCluster_Job($job_id);

$this->smarty->assign('job', $job);

?>
