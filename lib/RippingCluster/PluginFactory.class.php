<?php

abstract class RippingCluster_PluginFactory implements RippingCluster_IPluginFactory {
    
    static private $validPlugins = array();
    
    protected static function ensureScanned() {
        if (! isset(self::$validPlugins[get_called_class()])) {
            static::scan();
        }
    }
    
    protected static function isValidPlugin($plugin) {
        return isset(self::$validPlugins[get_called_class()][$plugin]);
    }
    
    public static function getValidPlugins() {
        static::ensureScanned();
        return array_keys(self::$validPlugins[get_called_class()]);
    }
    
    protected static function findPlugins($directory) {
        $plugins = array();
        
        $iterator = new RippingCluster_Utility_ClassFilesIterator(new RippingCluster_Utility_VisibleFilesIterator(new DirectoryIterator(RippingCluster_Lib . $directory)));
        
        foreach ($iterator as /** @var SplFileInfo */ $file) {
            $plugin = preg_replace('/.class.php$/', '', $file->getFilename());
            $plugins[] = $plugin;
        }
        
        return $plugins;
    }
    
    protected static function loadPlugins($plugins, $prefix, $interface) {
        self::$validPlugins[get_called_class()] = array();
        
        foreach ($plugins as $plugin) {
            $fullClassname = $prefix . $plugin;
            if ( ! class_exists($fullClassname, true)) {
                continue;
            }
            
            if ( ! in_array($interface, class_implements($fullClassname))) {
                continue;
            }
            
            // Initialise the plugin
            call_user_func(array($fullClassname, 'init'));
        
            self::$validPlugins[get_called_class()][$plugin] = $fullClassname;
        }
    }
    
    public static function classname($plugin) {
        static::ensureScanned();
        
        if ( ! self::isValidPlugin($plugin)) {
            throw new RippingCluster_Exception_InvalidPluginName($plugin);
        }
        
        return self::$validPlugins[get_called_class()][$plugin];
    }
    
}

?>