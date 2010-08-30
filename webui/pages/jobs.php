<?php

$main   = RippingCluster_Main::instance();
$req    = $main->request();
$config = $main->config();

if ($req->get('submit')) {
    $action =  RippingCluster_Main::issetelse($_POST['action'], 'RippingCluster_Exception_InvalidParameters');

    # If a bulk action was selected, the action will be a single term, otherwise it will also contain
    # the id of the single item to act upon. Work out which was used now.
    $matches = $job_ids = array();
    if (preg_match('/^(.*)\[(\d+)\]$/', $action, $matches)) {
        $action = $matches[1];
        $job_ids = array($matches[2]);
    }
    else {
        $job_ids = $_POST['include'];
    }

    $jobs = array();
    foreach ($job_ids as $job_id) {
        $job = RippingCluster_Job::fromId($job_id);
        if (!$job) {
            throw new RippingCluster_Exception_InvalidParameters('job_id');
        }
        $jobs[] = $job;
    }

    switch ($action) {
        case 'mark-failed': {
            foreach ($jobs as $job) {
                $job->updateStatus(RippingCluster_JobStatus::FAILED);
            }
        } break;

        case 'retry': {
            # Clone each of the selected jobs
            foreach ($jobs as $job) {
                $new_job = clone $job;
            }

            # Dispatch all the jobs in one run
            RippingCluster_Job::runAllJobs();

            # Redirect to the job queued page to show the jobs were successfully dispatched
            RippingCluster_Page::redirect('rips/setup-rip/queued');
        } break;

        case 'delete': {
            foreach ($jobs as $job) {
                $job->delete();
            }
        } break;

        default: {
            throw new RippingCluster_Exception_InvalidParameters('action');
        }
    }

    RippingCluster_Page::redirect('jobs');

} else {

    if (isset($_POST['view'])) {
        $statusName = urlencode($_POST['view']);
        RippingCluster_Page::redirect("jobs/view/{$statusName}");
    }

    $statusName = $req->get('view', 'any');
    switch ($statusName) {
        case 'any':      $status = null; break;
        case 'queued':   $status = RippingCluster_JobStatus::QUEUED; break;
        case 'running':  $status = RippingCluster_JobStatus::RUNNING; break;
        case 'complete': $status = RippingCluster_JobStatus::COMPLETE; break;
        case 'failed':   $status = RippingCluster_JobStatus::FAILED; break;
        default: throw new RippingCluster_Exception_InvalidParameters('view');
    } 

    $jobs = array();
    if ($status) {
        $jobs = RippingCluster_Job::allWithStatus($status);
    } else {
        $jobs = RippingCluster_Job::all();
    }

    $this->smarty->assign('jobs', $jobs);
}

?>
