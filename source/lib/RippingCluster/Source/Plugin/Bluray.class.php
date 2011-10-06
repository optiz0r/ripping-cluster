<?php

class RippingCluster_Source_Plugin_Bluray extends RippingCluster_PluginBase implements RippingCluster_Source_IPlugin {
    
    /**
     * Name of this plugin
     * @var string
     */
    const PLUGIN_NAME = "Bluray";
    
    /**
     * Returns a list of all Sources discovered by this plugin.
     * 
     * The sources are not scanned until specifically requested.
     * 
     * @return array(RippingCluster_Source)
     */
    public static function enumerate() {
        $config = RippingCluster_Main::instance()->config();
        $directories = $config->get('source.bluray.dir');
        
        $sources = array();    
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                throw new RippingCluster_Exception_InvalidSourceDirectory($directory);
            }
            
            $iterator = new RippingCluster_Utility_BlurayDirectoryIterator(new RippingCluster_Utility_VisibleFilesIterator(new DirectoryIterator($directory)));
            foreach ($iterator as /** @var SplFileInfo */ $source_vts) {
                $sources[] = self::load($source_vts->getPathname(), false);
            }
        }
        
        return $sources;
    }
    
    /**
     * Creates an object to represent the given source.
     * 
     * The source is not actually scanned unless specifically requested.
     * An unscanned object cannot be used until it has been manually scanned.
     * 
     * If requested, the source can be cached to prevent high load, and long scan times.
     * 
     * @param string $source_filename Filename of the source
     * @param bool $scan Request that the source be scanned for content. Defaults to true.
     * @param bool $use_cache Request that the cache be used. Defaults to true.
     * @return RippingCluster_Source
     */
    public static function load($source_filename, $scan = true, $use_cache = true) {
        $cache = RippingCluster_Main::instance()->cache();

        // Ensure the source is a valid directory, and lies below the configured source_dir
        if ( ! self::isValidSource($source_filename)) {
            return new RippingCluster_Source($source_filename, self::name(), false);
        }
            
        $source = null;
        if ($use_cache && $cache->exists($source_filename)) {
            $source = unserialize($cache->fetch($source_filename));
        } else {
            $source = new RippingCluster_Source($source_filename, self::name(), true);
           
            // TODO Populate source object with content
            
            // If requested, store the new source object in the cache
            if ($use_cache) {
                $source->cache();
            }
        }
        
        return $source;
    }
    
    /**
     * Creates an object to represent the given source using an encoded filename.
     * 
     * Wraps the call to load the source after the filename has been decoded.
     * 
     * @param string $encoded_filename Encoded filename of the source
     * @param bool  $scan Request that the source be scanned for content. Defaults to true.
     * @param bool $use_cache Request that the cache be used. Defaults to true.
     * @return RippingCluster_Source
     * 
     * @see RippingCluster_Source_IPlugin::load()
     */
    public static function loadEncoded($encoded_filename, $scan = true, $use_cache = true) {
        // Decode the filename
        $source_filename = base64_decode(str_replace('-', '/', $encoded_filename));

        return self::load($source_filename, $scan, $use_cache);
    }
    
    /**
     * Determins if a filename is a valid source loadable using this plugin
     * 
     * @param string $source_filename Filename of the source
     * @return bool
     */
    public static function isValidSource($source_filename) {
        $config = RippingCluster_Main::instance()->config();
        
        // Ensure the source is a valid directory, and lies below the configured source_dir
        if ( ! is_dir($source_filename)) {
            return false;
        }
        $real_source_filename = realpath($source_filename);
        
            // Check all of the source directories specified in the config
        $source_directories = $config->get('source.bluray.dir');
        foreach ($source_directories as $source_basedir) { 
            $real_source_basedir = realpath($source_basedir);
            
            if (substr($real_source_filename, 0, strlen($real_source_basedir)) != $real_source_basedir) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Permanently deletes the given source from disk
     * 
     * @param RippingCluster_Source $source Source object to be deleted
     * @return bool
     */
    public static function delete($source_filename) {
        if ( ! self::isValidSource($source_filename)) {
            return false;
        }
        
        return RippingCluster_Main::rmdir_recursive($source_filename);
    }

}

?>