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
        
        $this->gearman = new GearmanWorker();
        $this->gearman->addServers($config->get('rips.job_servers'));
        
        // Load all the plugin classes
        RippingCluster_Worker_PluginFactory::scan();
        foreach (RippingCluster_Worker_PluginFactory::getValidPlugins() as $plugin) {
            $workerFunctions = RippingCluster_Worker_PluginFactory::getPluginWorkerFunctions($plugin);
            
            foreach ($workerFunctions as $function => $callback) {
                $this->gearman->addFunction($function, $callback);
            }
        }
    }
    
   public function start() {
        while($this->gearman->work()) {
            if ($this->gearman->returnCode() != GEARMAN_SUCCESS) {
                break;
            }
        }
        
        return true;
    }
        
}

?>