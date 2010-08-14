<?php

require 'smarty/Smarty.class.php';

class HandBrakeCluster_Main {

    private static $instance;

    private $smarty;
    private $config;
    private $database;
    private $log;
    private $request;
    private $cache;
    
    private $base_uri;

    private function __construct() {
        $request_string = isset($_GET['l']) ? $_GET['l'] : '';
        
        $log_table = null;
        switch(HBC_File) {
            case 'index': {
                $log_table = 'client_log';
            } break;
            
            case 'worker': {
                $log_table = 'worker_log';
            }
        }

        $this->config   = new HandBrakeCluster_Config(HandBrakeCluster_DBConfig);
        $this->database = new HandBrakeCluster_Database($this->config);
        $this->config->setDatabase($this->database);

        $this->log      = new HandBrakeCluster_Log($this->database, $this->config, $log_table);
        $this->request  = new HandBrakeCluster_RequestParser($request_string);
        $this->cache    = new HandBrakeCluster_Cache($this->config);
        
        $this->smarty = new Smarty();
        $this->smarty->template_dir = './templates';
        $this->smarty->compile_dir  = './tmp/templates';
        $this->smarty->cache_dir    = './tmp/cache';
        $this->smarty->config_fir   = './config';

        $this->smarty->register_modifier('formatDuration', array('HandBrakeCluster_Main', 'formatDuration'));

        $this->smarty->assign('version', '0.1');
        
        $this->base_uri = dirname($_SERVER['SCRIPT_NAME']) . '/';
        $this->smarty->assign('base_uri', $this->base_uri);

    }

    /**
     * 
     * @return HandBrakeCluster_Main
     */
    public static function instance() {
        if (!self::$instance) {
            self::$instance = new HandBrakeCluster_Main();
        }

        return self::$instance;
    }

    public function smarty() {
        return $this->smarty;
    }

    /**
     * 
     * @return HandBrakeCluster_Config
     */
    public function config() {
        return $this->config;
    }

    /**
     * 
     * @return HandBrakeCluster_Database
     */
    public function database() {
        return $this->database;
    }

    /**
     * 
     * @return HandBrakeCluster_Log
     */
    public function log() {
        return $this->log;
    }

    /**
     * 
     * @return HandBrakeCluster_RequestParser
     */
    public function request() {
        return $this->request;
    }
    
    /**
     * 
     * @return HandBrakeCluster_Cache
     */
    public function cache() {
        return $this->cache;
    }
    
    public function baseUri() {
        return $this->base_uri;
    }
    
    public function absoluteUrl($relative_url) {
        $secure = isset($_SERVER['secure']);
        $port = $_SERVER['HTTP_PORT'];
        return 'http' . ($secure ? 's' : '') . '://'
            . $_SERVER['HTTP_HOST'] . (($port == 80 || ($secure && $port == 443)) ? '' : ':' . $port)
            . '/' . $this->base_uri . $relative_url; 
    }

    public static function initialise() {
        spl_autoload_register(array('HandBrakeCluster_Main','autoload'));
    }
    
    public static function autoload($classname) {
        // Ensure the classname contains only valid class name characters
        if (!preg_match('/^[A-Z][a-zA-Z0-9_]*$/', $classname)) {
            throw new Exception('Illegal characters in classname'); // TODO Subclass this exception
        }

        // Ensure the class to load begins with our prefix
        if (!preg_match('/^HandBrakeCluster_/', $classname)) {
            return;
        }

        // Special case: All exceptions are stored in the same file
        if (preg_match('/^HandBrakeCluster_Exception/', $classname)) {
            require_once(HandBrakeCluster_Lib . 'HandBrakeCluster/Exceptions.class.php');
            return;
        }

        // Replace any underscores with directory separators
        $filename = HandBrakeCluster_Lib . preg_replace('/_/', '/', $classname);

        // Tack on the class file suffix
        $filename .= '.class.php';

        // If this file exists, load it
        if (file_exists($filename)) {
            require_once $filename;
        }
    }
    
    public static function mkdir_recursive($directory, $permissions=0777) {
        $parts = explode('/', $directory);
        $path = '';
        for ($i=1,$l=count($parts); $i<=$l; $i++) {
            $iPath = $parts;
            $path = join('/', array_slice($iPath, 0, $i));
            if (empty($path)) continue;
            if (!file_exists($path)) {
                if (!mkdir($path)) return false;
                if (!chmod($path, $permissions)) return false;
            }
        }
        return true;
    }
    
    public static function issetelse($var, $default = null) {
        if (isset($var)) {
            return $var;
        }
        
        if (is_string($default) && preg_match('/^HandBrakeCluster_Exception/', $default) && class_exists($default) && is_subclass_of($default, HandBrakeCluster_Exception)) {
            throw new $default();
        }
        
        return $default;
    }

    public static function formatDuration($time) {
        if (is_null($time)) {
            return 'unknown';
        }

        $labels = array('seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years');
        $limits = array(60, 3600, 86400, 604800, 2592000, 31556926, PHP_INT_MAX);

        $working_time = $time;

        $result = "";
        $ptr = count($labels) - 1;

        while ($ptr >= 0 && $working_time < $limits[$ptr]) {
            --$ptr;
        }

        while ($ptr >= 0) {
            $unit_time = floor($working_time / $limits[$ptr]);
            $working_time -= $unit_time * $limits[$ptr];
            $result = $result . ' ' . $unit_time . ' ' . $labels[$ptr];
            --$ptr;
        }

        return $result;
    }
    
}

HandBrakeCluster_Main::initialise();

?>