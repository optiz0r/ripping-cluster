<?php

class HandBrakeCluster_JobStatus {

    const QUEUED = 0;
    const FAILED = 1;
    const RUNNING = 2;
    const COMPLETE = 3;

    private static $status_names = array(
        self::QUEUED => 'Queued',
        self::FAILED => 'Failed',
        self::RUNNING => 'Running',
        self::COMPLETE => 'Complete'
    );

    protected $id;
    protected $job_id;
    protected $status;
    protected $ctime;

    protected function __construct($id, $job_id, $status, $ctime) {
        $this->id     = $id;
        $this->job_id = $job_id;
        $this->status = $status;
        $this->ctime  = $ctime;
    }

    public static function fromDatabaseRow($row) {
        return new HandBrakeCluster_JobStatus(
            $row['id'],
            $row['job_id'],
            $row['status'],
            $row['ctime']
        );
    }

    public static function allForJob($job_id) {
        $statuses = array();

        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM job_status WHERE job_id=:job_id ORDER BY ctime ASC', array(
                array('name' => 'job_id', 'value' => $job_id, 'type' => PDO::PARAM_INT),
            )) as $row) {
            $statuses[] = HandBrakeCluster_JobStatus::fromDatabaseRow($row);
        }

        return $statuses;
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

};

?>
