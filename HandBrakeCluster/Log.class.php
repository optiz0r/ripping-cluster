<?php

class HandBrakeCluster_Log {

    private static $hostname = '';
    
    private $database;
    private $config;

    public function __construct(HandBrakeCluster_Database $database, HandBrakeCluster_Config $config) {
        $this->database = $database;
        $this->config = $config;

    }

    public function log($severity, $message, $job_id = 0) {
        $result = $this->database->insert('INSERT INTO client_log (job_id,level,ctime,pid,hostname,progname,line,message) VALUES(:job_id, :level, :ctime, :pid, :hostname, :progname, :line, :message)',
            array(
                array('name' => 'job_id', 'value' => $job_id, 'type' => PDO::PARAM_INT),
                array('name' => 'level', 'value' => $severity, 'type' => PDO::PARAM_STR),
                array('name' => 'ctime', 'value' => time(), 'type' => PDO::PARAM_INT),
                array('name' => 'pid', 'value' => 0, 'type' => PDO::PARAM_INT),
                array('name' => 'hostname', 'value' => self::$hostname, 'type' => PDO::PARAM_STR),
                array('name' => 'progname', 'value' => 'webui', 'type' => PDO::PARAM_STR),
                array('name' => 'line', 'value' => 0, 'type' => PDO::PARAM_INT),
                array('name' => 'message', 'value' => $message, 'type' => PDO::PARAM_STR)
            )
        );

        if (!$result) {
            var_dump($this->database->errorInfo());
        }
    }

    public function debug($message, $job_id = 0) {
        return $this->log('DEBUG', $message, $job_id);
    }

    public function info($messgae, $job_id = 0) {
        return $this->log('INFO', $message, $job_id);
    }

    public function warning($message, $job_id = 0) {
        return $this->log('WARNING', $message, $job_id);
    }

    public function error($message, $job_id = 0) {
        return $this->log('ERROR', $message, $job_id);
    }

    public static function initialise() {
        self::$hostname = trim(`hostname`);
    }

}

HandBrakeCluster_Log::initialise();

?>
