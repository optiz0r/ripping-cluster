<?php

require 'smarty/Smarty.class.php';

class HandBrakeCluster_Main {

    private static $instance;

    private $smarty;
    private $config;
    private $database;
    private $log;
    private $request;

    private function __construct() {
        $request_string = isset($_GET['l']) ? $_GET['l'] : '';

        $this->config   = new HandBrakeCluster_Config("dbconfig.conf");
        $this->database = new HandBrakeCluster_Database($this->config);
        $this->config->setDatabase($this->database);

        $this->log      = new HandBrakeCluster_Log($this->database, $this->config);
        $this->request = new HandBrakeCluster_RequestParser($request_string);

        $this->smarty = new Smarty();
        $this->smarty->template_dir = './templates';
        $this->smarty->compile_dir  = './tmp/templates';
        $this->smarty->cache_dir    = './tmp/cache';
        $this->smarty->config_fir   = './config';

        $this->smarty->assign('version', '0.1');
        $this->smarty->assign('base_uri', dirname($_SERVER['SCRIPT_NAME']) . '/');

    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new HandBrakeCluster_Main();
        }

        return self::$instance;
    }

    public function smarty() {
        return $this->smarty;
    }

    public function config() {
        return $this->config;
    }

    public function database() {
        return $this->database;
    }

    public function log() {
        return $this->log;
    }

    public function request() {
        return $this->request;
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
        if (preg_match('/^HandBrakeCluster_Exception_/', $classname)) {
            require_once('HandBrakeCluster/Exceptions.class.php');
            return;
        }

        // Replace any underscores with directory separators
        $filename = preg_replace('/_/', '/', $classname);

        // Tack on the class file suffix
        $filename .= '.class.php';

        // If this file exists, load it
        if (file_exists($filename)) {
            require_once $filename;
        }
    }
}

HandBrakeCluster_Main::initialise();

?>
