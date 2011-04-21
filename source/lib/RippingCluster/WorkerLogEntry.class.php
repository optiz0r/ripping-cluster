<?php

class RippingCluster_WorkerLogEntry extends RippingCluster_LogEntry {

    protected $jobId;
    
    protected function __construct($id, $level, $ctime, $pid, $hostname, $progname, $line, $message, $jobId) {
        parent::__construct($id, $level, $ctime, $pid, $hostname, $progname, $line, $message);
        
        $this->jobId = $jobId;
    }
    
    public static function fromDatabaseRow($row) {
        return new self(
            $row['id'],
            $row['level'],
            $row['ctime'],
            $row['pid'],
            $row['hostname'],
            $row['progname'],
            $row['line'],
            $row['message'],
            $row['job_id']
        );
    }

    public static function initialise() {
        parent::$table_name = 'worker_log';
    }
    
    public function jobId() {
        return $this->jobId;
    }
    
};

RippingCluster_WorkerLogEntry::initialise();

?>
