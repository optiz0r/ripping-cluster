<?php

class RippingCluster_LogEntry extends SihnonFramework_LogEntry {
    
    protected $job_id;
    
    protected static $types;
    
    public static function initialise() {
    	// Copy the list of datatypes from the parent
    	// We can't modify it in place, else we'll break any logging done inside the SihnonFramework tree
    	// or other subclass trees.
    	static::$types = parent::$types;
    	
    	// Add the new data types for this subclass
        static::$types['job_id'] = 'int'; 
    }

    protected function __construct($level, $category, $ctime, $pid, $file, $line, $message, $job_id) {
        parent::__construct($level, $category, $ctime, $pid, $file, $line, $message);
        
        $this->job_id = $job_id;
    }
    
    public static function fromArray($row) {
        return new self(
            $row['level'],
            $row['category'],
            $row['ctime'],
            $row['pid'],
            $row['file'],
            $row['line'],
            $row['message'],
            $row['job_id']
        );
    }
 
    public function values() {
        return array(
            $this->level,
            $this->category,
            $this->ctime,
            static::$hostname,
            static::$progname,
            $this->pid,
            $this->file,
            $this->line,
            $this->message,
            $this->job_id,
        );
    }
    
    public function jobId() {
        return $this->job_id;
    }
    
    protected static function log($logger, $severity, $job_id, $message, $category = SihnonFramework_Log::CATEGORY_DEFAULT) {
        $backtrace = debug_backtrace(false);
        $entry = new self($severity, $category, time(), getmypid(), $backtrace[1]['file'], $backtrace[1]['line'], $message, $job_id);
        
        $logger->log($entry);
    }
    
    public static function debug($logger, $job_id, $message, $category = SihnonFramework_Log::CATEGORY_DEFAULT) {
        static::log($logger, SihnonFramework_Log::LEVEL_DEBUG, $job_id, $message, $category);
    }

    public static function info($logger, $job_id, $message, $category = SihnonFramework_Log::CATEGORY_DEFAULT) {
        static::log($logger, SihnonFramework_Log::LEVEL_INFO, $job_id, $message, $category);
    }

    public static function warning($logger, $job_id, $message, $category = SihnonFramework_Log::CATEGORY_DEFAULT) {
        static::log($logger, SihnonFramework_Log::LEVEL_WARNING, $job_id, $message, $category);
    }

    public static function error($logger, $job_id, $message, $category = SihnonFramework_Log::CATEGORY_DEFAULT) {
        static::log($logger, SihnonFramework_Log::LEVEL_ERROR, $job_id, $message, $category);
    }
        
}

RippingCluster_LogEntry::initialise();

?>