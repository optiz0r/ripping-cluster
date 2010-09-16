<?php

class RippingCluster_Config {
    
    /**
     * Boolean value type
     * @var bool
     */
    const TYPE_BOOL        = 'bool';
    
    /**
     * Integer value type
     * @var int
     */
    const TYPE_INT         = 'int';
    
    /**
     * Float value type
     * @var float
     */
    const TYPE_FLOAT       = 'float';
    
    /**
     * String value type
     * @var string
     */
    const TYPE_STRING      = 'string';
    
    /**
     * String List value type; list of newline separated strings
     * @var array(string)
     */
    const TYPE_STRING_LIST = 'array(string)';

    /**
     * Contents of the dbconfig file 
     * @var string
     */
    private $dbconfig;
    
    /**
     * Database object created for the lifetime of this script
     * @var RippingCluster_Database
     */
    private $database;

    /**
     * Associative array of connection parameters for the database configuration
     * @var array(string=>string)
     */
    private $databaseConfig = array();
    
    /**
     * Associative array of settings loaded from the database
     * @var array(string=>array(string=>string))
     */
    private $settings       = array();

    /**
     * Constructs a new instance of the Config class
     *
     * @param string $dbconfig Database configuration file contents
     * @return RippingCluster_Config
     */
    public function __construct($dbconfig) {
        $this->dbconfig = $dbconfig;
        
        $this->parseDatabaseConfig();
    }

    /**
     * Parses the contents of the database configuration file so that individual settings can be retrieved.
     *
     */
    public function parseDatabaseConfig() {
        $this->databaseConfig = parse_ini_file($this->dbconfig);
    }

    /**
     * Returns the value of the named item from the database configuration file
     *
     * @param string $key Name of the setting to retrieve
     */
    public function getDatabase($key) {
        if (!isset($this->databaseConfig[$key])) {
            throw new RippingCluster_Exception_DatabaseConfigMissing($key);
        }

        return $this->databaseConfig[$key];
    }

    /**
     * Sets the database instance used by this object
     * 
     * @param RippingCluster_Database $database Database instance
     */
    public function setDatabase(RippingCluster_Database $database) {
        $this->database = $database;
        $this->preload();
    }

    /**
     * Loads the entire list of settings from the database
     * 
     */
    private function preload() {
        if (!$this->database) {
            throw new RippingCluster_Exception_NoDatabaseConnection();
        }

        $this->settings = $this->database->selectAssoc('SELECT name,type,value FROM settings', 'name', array('name', 'value', 'type'));
    }

    /**
     * Identifies whether the named setting exists
     * 
     * @param string $key Name of the setting
     * @return bool
     */
    public function exists($key) {
        return isset($this->settings[$key]);
    }

    /**
     * Fetches the value of the named setting
     * 
     * @param string $key Name of the setting
     */
    public function get($key) {
        if (!isset($this->settings[$key])) {
            throw new RippingCluster_Exception_UnknownSetting($key);
        }

        switch ($this->settings[$key]['type']) {
            case TYPE_STRING_LIST:
                return explode("\n", $this->settings[$key]['value']);
                
            default:
               return $this->settings[$key]['value'];
        }
    }

};

?>
