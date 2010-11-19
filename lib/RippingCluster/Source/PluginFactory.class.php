<?php

class RippingCluster_Source_PluginFactory extends RippingCluster_PluginFactory {
    
    const PLUGIN_DIR       = 'RippingCluster/Source/Plugin/';
    const PLUGIN_PREFIX    = 'RippingCluster_Source_Plugin_';
    const PLUGIN_INTERFACE = 'RippingCluster_Source_IPlugin';
    
    public static function init() {
        
    }
    
    public static function scan() {
        $candidatePlugins   = parent::findPlugins(self::PLUGIN_DIR);
        
        self::loadPlugins($candidatePlugins, self::PLUGIN_PREFIX, self::PLUGIN_INTERFACE);
    }
    
    public static function enumerate($plugin) {
        self::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'enumerate'));
    }
    
    public static function enumerateAll() {
        self::ensureScanned();
        
        $sources = array();
        foreach (self::getValidPlugins() as $plugin) {
            $sources[$plugin] = self::enumerate($plugin);
        }
        
        return $sources;
    }
    
    public static function load($plugin, $source_filename, $scan = true, $use_cache = true) {
        self::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'load'), $source_filename, $scan, $use_cache);
    }
    
    public static function loadEncoded($plugin, $encoded_filename, $scan = true, $use_cache = true) {
        self::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'loadEncoded'), $encoded_filename, $scan, $use_cache);
    }
    
    public static function isValidSource($plugin, $source_filename) {
        self::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'isValidSource'), source_filename);
    }
    
    /**
     * Permanently deletes the given source from disk
     *
     * @param string $plugin Name of the plugin used to load the source
     * @param string $source_filename Filename of the source to be deleted
     */
    public static function delete($plugin, $source_filename) {
        self::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            return null;
        }
        
        return call_user_func(array(self::classname($plugin), 'delete'), $source_filename);
    }
    
}

?>