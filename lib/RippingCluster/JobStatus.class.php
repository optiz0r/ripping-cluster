<?php

class RippingCluster_JobStatus {

    const CREATED  = 0;
    const QUEUED   = 1;
    const FAILED   = 2;
    const RUNNING  = 3;
    const COMPLETE = 4;

    private static $status_names = array(
        self::CREATED  => 'Created',
        self::QUEUED   => 'Queued',
        self::FAILED   => 'Failed',
        self::RUNNING  => 'Running',
        self::COMPLETE => 'Complete',
    );

    protected $id;
    protected $job_id;
    protected $status;
    protected $ctime;
    protected $mtime;
    protected $rip_progress;

    protected function __construct($id, $job_id, $status, $ctime, $mtime, $rip_progress) {
        $this->id     = $id;
        $this->job_id = $job_id;
        $this->status = $status;
        $this->ctime  = $ctime;
        $this->mtime  = $mtime;
        $this->rip_progress = $rip_progress;
    }

    public static function fromDatabaseRow($row) {
        return new RippingCluster_JobStatus(
            $row['id'],
            $row['job_id'],
            $row['status'],
            $row['ctime'],
            $row['mtime'],
            $row['rip_progress']
        );
    }
    
    public static function updateStatusForJob($job, $status, $rip_progress = null) {
        $ctime = $mtime = time();
        $status = new RippingCluster_JobStatus(null, $job->id(), $status, $ctime, $mtime, $rip_progress);
        $status->create();
        
        return $status;
    }
    
    public function updateRipProgress($rip_progress) {
        $this->rip_progress = $rip_progress;
        $this->mtime        = time();
        $this->save();
    }

    public static function allForJob(RippingCluster_Job $job) {
        $statuses = array();

        $database = RippingCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM job_status WHERE job_id=:job_id ORDER BY mtime ASC', array(
                array('name' => 'job_id', 'value' => $job->id(), 'type' => PDO::PARAM_INT),
            )) as $row) {
            $statuses[] = RippingCluster_JobStatus::fromDatabaseRow($row);
        }

        return $statuses;
    }
    
    protected function create() {
        $database = RippingCluster_Main::instance()->database();
        $database->insert(
        	'INSERT INTO job_status
        	(id, job_id, status, ctime, mtime, rip_progress)
        	VALUES(NULL,:job_id,:status,:ctime,:mtime,:rip_progress)',
            array(
                array('name' => 'job_id',       'value' => $this->job_id,       'type' => PDO::PARAM_INT),
                array('name' => 'status',       'value' => $this->status,       'type' => PDO::PARAM_INT),
                array('name' => 'ctime',        'value' => $this->ctime,        'type' => PDO::PARAM_INT),
                array('name' => 'mtime',        'value' => $this->mtime,        'type' => PDO::PARAM_INT),
                array('name' => 'rip_progress', 'value' => $this->rip_progress),
            )
        );
        
        $this->id = $database->lastInsertId();
    }
    
    public function save() {
        $database = RippingCluster_Main::instance()->database();
        $database->update(
        	'UPDATE job_status SET
        	job_id=:job_id, status=:status, ctime=:ctime, mtime=:mtime, rip_progress=:rip_progress
        	WHERE id=:id',
            array(
                array('name' => 'id',           'value' => $this->id,           'type' => PDO::PARAM_INT),
                array('name' => 'job_id',       'value' => $this->job_id,       'type' => PDO::PARAM_INT),
                array('name' => 'status',       'value' => $this->status,       'type' => PDO::PARAM_INT),
                array('name' => 'ctime',        'value' => $this->ctime,        'type' => PDO::PARAM_INT),
                array('name' => 'mtime',        'value' => $this->mtime,        'type' => PDO::PARAM_INT),
                array('name' => 'rip_progress', 'value' => $this->rip_progress),
            )
        );
    }
    
    public function hasProgressInfo() {
        return ($this->status == self::RUNNING);
    }

    public static function fixBrokenTimestamps() {
        $statuses = array();
        
        $database = RippingCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM job_status WHERE status=4 AND job_id IN (SELECT job_id FROM job_status WHERE status=3)') as $row) {
            $status = RippingCluster_JobStatus::fromDatabaseRow($row);
            $status->mtime = time();
            $status->save();
        }    
    }
    
    public function id() {
        return $this->id;
    }

    public function jobId() {
        return $this->job_id;
    }

    public function status() {
        return $this->status;
    }

    public function statusName() {
        return self::$status_names[$this->status];
    }

    public function ctime() {
        return $this->ctime;
    }

    public function mtime($new_mtime = null) {
        if ($new_mtime !== null) {
            $this->mtime = $new_mtime;
        }
        
        return $this->mtime;
    }
    
    public function ripProgress() {
        return $this->rip_progress;
    }

};

?>
