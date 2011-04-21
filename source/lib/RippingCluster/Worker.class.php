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
        
        $this->gearman = new Net_Gearman_Worker('river.sihnon.net:4730');//$config->get('rips.job_servers'));
        
        // Load all the plugin classes
        RippingCluster_Worker_PluginFactory::scan();
        $plugins = RippingCluster_Worker_PluginFactory::getValidPlugins();
        foreach ($plugins as $plugin) {
            $this->gearman->addAbility($plugin);
            
            //$workerFunctions = RippingCluster_Worker_PluginFactory::getPluginWorkerFunctions($plugin);
            //foreach ($workerFunctions as $function => $callback) {
            //    echo "Added ability $function\n";
            //    $this->gearman->addAbility($function);
            //}
        }
    }
    
   public function start() {
       try {
           $this->gearman->beginWork();
       } catch (Net_Gearman_Exception $e) {
           // Do stuff
       }
        
        return true;
    }
        
}

?>
