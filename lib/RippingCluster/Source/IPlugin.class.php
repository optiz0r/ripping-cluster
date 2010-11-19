<?php

interface RippingCluster_Source_IPlugin extends RippingCluster_IPlugin {
    
    /**
     * Returns a list of all Sources discovered by this plugin.
     * 
     * The sources are not scanned until specifically requested.
     * 
     * @return array(RippingCluster_Source)
     */
    public static function enumerate();
    
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
    public static function load($source_filename, $scan = true, $use_cache = true);
    
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
    public static function loadEncoded($encoded_filename, $scan = true, $use_cache = true);
    
    /**
     * Determines if a filename is a valid source loadable using this plugin
     * 
     * @param string $source_filename Filename of the source
     * @return bool
     */
    public static function isValidSource($source_filename);
    
    /**
     * Permanently deletes the given source from disk
     * 
     * @param RippingCluster_Source $source Source object to be deleted
     * @return bool
     */
    public static function delete($source_filename);
    
}

?>