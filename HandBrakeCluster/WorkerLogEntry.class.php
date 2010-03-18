<?php

class HandBrakeCluster_WorkerLogEntry extends HandBrakeCluster_LogEntry {

    public static function initialise() {
        parent::$table_name = 'worker_log';
    }

};

HandBrakeCluster_WorkerLogEntry::initialise();

?>
