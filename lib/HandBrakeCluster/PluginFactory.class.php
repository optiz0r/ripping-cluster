<?php

abstract class HandBrakeCluster_PluginFactory {
    
    static protected $validPlugins;
    
    abstract public static function init();
    
    public static function getValidPlugins() {
        return array_keys(self::$validPlugins);
    }
    
    protected static function findPlugins($directory) {
        $plugins = array();
        
        $iterator = new HandBrakeCluster_Utility_ClassFilesIterator(new HandBrakeCluster_Utility_VisibleFilesIterator(new DirectoryIterator(HandBrakeCluster_Lib . $directory)));
        
        foreach ($iterator as /** @var SplFileInfo */ $file) {
            $plugin = preg_replace('/.class.php$/', '', $file->getFilename());
            $plugins[] = $plugin;
        }
        
        return $plugins;
    }
    
    protected static function loadPlugins($plugins, $prefix) {
        self::$validPlugins = array();
        
        foreach ($plugins as $plugin) {
            $fullClassname = $prefix . $plugin;
            if ( ! class_exists($fullClassname, true)) {
                echo "Cannot load $fullClassname\n";
                continue;
            }
            
            if ( ! in_array('HandBrakeCluster_Worker_IPlugin', class_implements($fullClassname))) {
                echo "$plugin does not implement the necessary interfaces\n";
                continue;
            }
            
            // Initialise the plugin
            call_user_func(array($fullClassname, 'init'));
        
            self::$validPlugins[$plugin] = $fullClassname;
        }
    }
    
}

?>