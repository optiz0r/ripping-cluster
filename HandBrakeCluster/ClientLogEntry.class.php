<?php

class HandBrakeCluster_ClientLogEntry extends HandBrakeCluster_LogEntry {

    public static function initialise() {
        parent::$table_name = 'client_log';
    }

};

HandBrakeCluster_ClientLogEntry::initialise();

?>
