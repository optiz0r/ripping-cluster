<?php

$jobs = HandBrakeCluster_Job::all();
$this->smarty->assign('jobs', $jobs);

?>
