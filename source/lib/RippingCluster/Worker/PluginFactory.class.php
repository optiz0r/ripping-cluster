<?php

class RippingCluster_Worker_PluginFactory extends RippingCluster_PluginFactory {
    
    const PLUGIN_DIR       = 'Net/Gearman/Job/';
    const PLUGIN_PREFIX    = 'Net_Gearman_Job_';
    const PLUGIN_INTERFACE = 'RippingCluster_Worker_IPlugin';
    
    public static function init() {
        
    }
    
    public static function scan() {
        $candidatePlugins   = parent::findPlugins(self::PLUGIN_DIR);
        
        parent::loadPlugins($candidatePlugins, self::PLUGIN_PREFIX, self::PLUGIN_INTERFACE);
    }
    
    public static function getPluginWorkerFunctions($plugin) {
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'workerFunctions'));
    }
}

?>