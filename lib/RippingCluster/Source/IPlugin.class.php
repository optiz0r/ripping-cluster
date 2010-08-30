<?php

interface RippingCluster_Source_IPlugin extends RippingCluster_IPlugin {
    
    public static function enumerate();
    
    public static function load($source_filename, $scan = true, $use_cache = true);
    
    public static function loadEncoded($encoded_filename, $scan = true, $use_cache = true);
    
    public static function isValidSource($source_filename);
    
}

?>