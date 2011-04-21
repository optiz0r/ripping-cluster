<?php

interface RippingCluster_Worker_IPlugin extends RippingCluster_IPlugin {
    
    /**
     * Returns the list of functions (and names) implemented by this plugin for registration with Gearman
     * 
     * @return array(string => callback)
     */
    //public static function workerFunctions();

    //public static function run($args);

}

?>