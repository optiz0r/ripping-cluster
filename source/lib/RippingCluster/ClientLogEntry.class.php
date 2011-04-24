<?php

class RippingCluster_ClientLogEntry extends RippingCluster_LogEntry {

    public static function debug($logger, $job_id, $message) {
        static::log($logger, SihnonFramework_Log::LEVEL_DEBUG, $job_id, $message, 'client');
    }

    public static function info($logger, $job_id, $message) {
        static::log($logger, SihnonFramework_Log::LEVEL_INFO, $job_id, $message, 'client');
    }

    public static function warning($logger, $job_id, $message) {
        static::log($logger, SihnonFramework_Log::LEVEL_WARNING, $job_id, $message, 'client');
    }

    public static function error($logger, $job_id, $message) {
        static::log($logger, SihnonFramework_Log::LEVEL_ERROR, $job_id, $message, 'client');
    }
    
};

?>