<?php

class RippingCluster_Worker_PluginFactory extends RippingCluster_PluginFactory {
    
    const PLUGIN_DIR = 'RippingCluster/Worker/Plugin/';
    const PREFIX     = 'RippingCluster_Worker_Plugin_';
    
    public static function init() {
        
    }
    
    public static function scan() {
        $candidatePlugins   = parent::findPlugins(self::PLUGIN_DIR);
        
        parent::loadPlugins($candidatePlugins, self::PREFIX);
    }
    
    public static function getPluginWorkerFunctions($plugin) {
        if ( ! isset(parent::$validPlugins[$plugin])) {
            return null;
        }
        
        return call_user_func(array(parent::$validPlugins[$plugin], 'workerFunctions'));
    }
}

?>