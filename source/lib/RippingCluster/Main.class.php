<?php

require 'smarty/Smarty.class.php';

class RippingCluster_Main extends SihnonFramework_Main {

    protected static $instance;

    protected $smarty;
    protected $request;

    protected function __construct() {
        parent::__construct();
        
        $request_string = isset($_GET['l']) ? $_GET['l'] : '';
        
        $this->request  = new RippingCluster_RequestParser($request_string);
        
        if (HBC_File == 'index') {
            $this->smarty = new Smarty();
            $this->smarty->template_dir = './source/templates';
            $this->smarty->compile_dir  = './tmp/templates';
            $this->smarty->cache_dir    = './tmp/cache';
            $this->smarty->config_dir   = './config';
    
            $this->smarty->registerPlugin('modifier', 'formatDuration', array('RippingCluster_Main', 'formatDuration'));

            $this->smarty->assign('version', '0.1');
            $this->smarty->assign('messages', array());
        
            $this->smarty->assign('base_uri', $this->base_uri);
        }
    }

    public function smarty() {
        return $this->smarty;
    }

    /**
     * 
     * @return RippingCluster_RequestParser
     */
    public function request() {
        return $this->request;
    }
    
    
}

?>