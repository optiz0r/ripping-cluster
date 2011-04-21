<?php

class RippingCluster_Worker_PluginFactory extends RippingCluster_PluginFactory {
    
    protected static $plugin_prefix    = 'Net_Gearman_Job_';
    protected static $plugin_interface = 'RippingCluster_Worker_IPlugin';
    protected static $plugin_dir       = array(
        RippingCluster_Lib => 'Net/Gearman/Job/',
    );
    
    public static function init() {
        
    }
    
/*    public static function scan() {
        $candidatePlugins   = parent::findPlugins(self::PLUGIN_DIR);
        
        parent::loadPlugins($candidatePlugins, self::PLUGIN_PREFIX, self::PLUGIN_INTERFACE);
}*/
    
    public static function getPluginWorkerFunctions($plugin) {
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'workerFunctions'));
    }
}

?>
