<?php

abstract class HandBrakeCluster_LogEntry {

    protected static $table_name = "";

    protected $id;
    protected $job_id;
    protected $level;
    protected $ctime;
    protected $pid;
    protected $hostname;
    protected $progname;
    protected $line;
    protected $message;
    
    protected function __construct($id, $job_id, $level, $ctime, $pid, $hostname, $progname, $line, $message) {
        $this->id       = $id;
        $this->job_id   = $job_id;
        $this->level    = $level;
        $this->ctime    = $ctime;
        $this->pid      = $pid;
        $this->hostname = $hostname;
        $this->progname = $progname;
        $this->line     = $line;
        $this->message  = $message;
    }

    public static function fromDatabaseRow($row) {
        return new HandBrakeCluster_ClientLogEntry(
            $row['id'],
            $row['job_id'],
            $row['level'],
            $row['ctime'],
            $row['pid'],
            $row['hostname'],
            $row['progname'],
            $row['line'],
            $row['message']
        );
    }

    public static function fromId($id) {
        $database = HandBrakeCluster_Main::instance()->database();
        return HandBrakeCluster_ClientLogEntry::fromDatabaseRow(
            $database->selectOne('SELECT * FROM '.self::$table_name.' WHERE id=:id', array(
                array('name' => 'id', 'value' => $id, 'type' => PDO::PARAM_INT)
                )
            )
        );
    }

    public static function recent($limit = 100) {
        $entries = array();

        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM '.self::$table_name.' ORDER BY ctime DESC LIMIT :limit', array(
                array('name' => 'limit', 'value' => $limit, 'type' => PDO::PARAM_INT)
            )) as $row) {
            $entries[] = self::fromDatabaseRow($row);
        }

        return $entries;
    }

    public static function recentForJob($job_id, $limit = 100) {
        $entries = array();
        
        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM '.self::$table_name.' WHERE job_id=:job_id ORDER BY ctime DESC LIMIT :limit', array(
                array('name' => 'job_id', 'value' => $job_id, 'type' => PDO::PARAM_INT),
                array('name' => 'limit', 'value' => $limit, 'type' => PDO::PARAM_INT)
            )) as $row) {
            $entries[] = self::fromDatabaseRow($row);
        }

        return $entries;
    }

    public static function allForNoJob() {
        return self::allForJob(0);
    }

    public function id() {
        return $this->id;
    }

    public function jobId() {
        return $this->job_id;
    }

    public function level() {
        return $this->level;
    }

    public function ctime() {
        return $this->ctime;
    }

    public function pid() {
        return $this->pid;
    }

    public function hostname() {
        return $this->hostname;
    }

    public function progname() {
        return $this->progname;
    }

    public function line() {
        return $this->line;
    }

    public function message() {
        return $this->message;
    }

};

?>
