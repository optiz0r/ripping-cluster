<?php

class RippingCluster_ClientLogEntry extends RippingCluster_LogEntry {

    public static function initialise() {
        parent::$table_name = 'client_log';
    }

};

RippingCluster_ClientLogEntry::initialise();

?>
