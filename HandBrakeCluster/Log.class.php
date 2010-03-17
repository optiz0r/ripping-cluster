<?php

class HandBrakeCluster_Log {

    private $database;
    private $config;

    public function __construct(HandBrakeCluster_Database $database, HandBrakeCluster_Config $config) {
        $this->database = $database;
        $this->config = $config;
    }

}

?>
