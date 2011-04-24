<?php

class RippingCluster_Worker {
    
    protected $gearman;
    
    public function __construct() {
        $this->init();
        
    }
    
    private function init() {
        if ($this->gearman) {
            return;
        }
        
        $config = RippingCluster_Main::instance()->config();
        
        $this->gearman = new Net_Gearman_Worker($config->get('rips.job_servers'));
        
        // Load all the plugin classes
        RippingCluster_Worker_PluginFactory::scan();
        $plugins = RippingCluster_Worker_PluginFactory::getValidPlugins();
        foreach ($plugins as $plugin) {
            $this->gearman->addAbility($plugin);
        }
    }
    
   public function start() {
       try {
           $this->gearman->beginWork();
       } catch (Net_Gearman_Exception $e) {
           RippingCluster_WorkerLogEntry::error(SihnonFramework_Main::instance()->log(), 0, $e->toText());
           return false;
       }
        
        return true;
    }
        
}

?>
