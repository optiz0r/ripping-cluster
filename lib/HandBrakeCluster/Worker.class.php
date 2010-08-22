<?php

class HandBrakeCluster_Worker {
    
    protected $gearman;
    
    public function __construct() {
        $this->init();
        
    }
    
    private function init() {
        if ($this->gearman) {
            return;
        }
        
        $config = HandBrakeCluster_Main::instance()->config();
        
        $this->gearman = new GearmanWorker();
        $this->gearman->addServers($config->get('rips.job_servers'));
        
        // Load all the plugin classes
        echo "Loading Plugins\n";
        HandBrakeCluster_Worker_PluginFactory::scan();
        foreach (HandBrakeCluster_Worker_PluginFactory::getValidPlugins() as $plugin) {
            echo "Grabbing worker functions provided by {$plugin}\n";
            $workerFunctions = HandBrakeCluster_Worker_PluginFactory::getPluginWorkerFunctions($plugin);
            
            foreach ($workerFunctions as $function => $callback) {
                echo "Adding {$plugin}::{$callback[1]} as {$function}\n";
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