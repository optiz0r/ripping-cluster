<?php

class HandBrakeCluster_Worker_PluginFactory extends HandBrakeCluster_PluginFactory {
    
    const PLUGIN_DIR = 'HandBrakeCluster/Worker/Plugin/';
    const PREFIX     = 'HandBrakeCluster_Worker_Plugin_';
    
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