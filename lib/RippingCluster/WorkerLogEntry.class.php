<?php

class RippingCluster_WorkerLogEntry extends RippingCluster_LogEntry {

    public static function initialise() {
        parent::$table_name = 'worker_log';
    }

};

RippingCluster_WorkerLogEntry::initialise();

?>
