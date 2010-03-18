<?php

$jobs = HandBrakeCluster_Job::all(HandBrakeCluster_Main::instance()->database());
$this->smarty->assign('jobs', $jobs);

?>
