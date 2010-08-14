<?php

class HandBrakeCluster_Config {

    private $dbconfig;
    private $database;

    private $databaseConfig = array();
    private $settings       = array();

    public function __construct($dbconfig) {
        $this->dbconfig = $dbconfig;
        
        $this->parseDatabaseConfig();
    }

    public function parseDatabaseConfig() {
        $this->databaseConfig = parse_ini_file($this->dbconfig);
    }

    public function getDatabase($key) {
        if (!isset($this->databaseConfig[$key])) {
            throw new HandBrakeCluster_Exception_DatabaseConfigMissing($key);
        }

        return $this->databaseConfig[$key];
    }

    public function setDatabase(HandBrakeCluster_Database $database) {
        $this->database = $database;
        $this->preload();
    }

    public function preload() {
        if (!$this->database) {
            throw new HandBrakeCluster_Exception_NoDatabaseConnection();
        }

        $this->settings = $this->database->selectAssoc('SELECT name,value FROM settings', 'name', 'value');
    }

    public function exists($key) {
        return isset($this->settings[$key]);
    }

    public function get($key) {
        if (!isset($this->settings[$key])) {
            throw new HandBrakeCluster_Exception_UnknownSetting($key);
        }

        return $this->settings[$key];
    }

};

?>
