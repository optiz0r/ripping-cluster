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

        switch (HBC_File) {
            case 'ajax':
            case 'index': {
                $smarty_tmp = '/var/tmp/ripping-cluster';
                $this->smarty = new Smarty();
                $this->smarty->template_dir = static::makeAbsolutePath('./source/templates');
                $this->smarty->compile_dir  = static::makeAbsolutePath($smarty_tmp . '/templates');
                $this->smarty->cache_dir    = static::makeAbsolutePath($smarty_tmp . '/cache');
                $this->smarty->config_dir   = static::makeAbsolutePath($smarty_tmp . '/config');
                $this->smarty->plugins_dir[]= static::makeAbsolutePath('./source/smarty/plugins');
                 
                $this->smarty->registerPlugin('modifier', 'formatDuration', array('RippingCluster_Main', 'formatDuration'));
                $this->smarty->registerPlugin('modifier', 'formatFilesize', array('RippingCluster_Main', 'formatFilesize'));

                $this->smarty->assign('version', '0.3');
                $this->smarty->assign('messages', array());
                 
                $this->smarty->assign('base_uri', $this->base_uri);
                $this->smarty->assign('base_url', static::absoluteUrl(''));
                
            } break;

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
