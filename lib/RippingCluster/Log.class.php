<?php

class RippingCluster_Log {

    private static $hostname = '';
    
    private $database;
    private $config;
    private $table;

    public function __construct(RippingCluster_Database $database, RippingCluster_Config $config, $table) {
        $this->database = $database;
        $this->config = $config;
        $this->table = $table;

    }

    public function log($severity, $message, $job_id = 0) {
        $this->database->insert("INSERT INTO {$this->table} (job_id,level,ctime,pid,hostname,progname,line,message) VALUES(:job_id, :level, :ctime, :pid, :hostname, :progname, :line, :message)",
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
        
        if (HBC_File == 'worker') {
            echo date("r") . ' ' . $message . "\n"; 
        }
    }

    public function debug($message, $job_id = 0) {
        return $this->log('DEBUG', $message, $job_id);
    }

    public function info($message, $job_id = 0) {
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

RippingCluster_Log::initialise();

?>
