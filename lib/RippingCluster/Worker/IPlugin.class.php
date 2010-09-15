<?php

interface RippingCluster_Worker_IPlugin extends RippingCluster_IPlugin {
    
    /**
     * Returns the list of functions (and names) implemented by this plugin for registration with Gearman
     * 
     * @return array(string => callback)
     */
    public static function workerFunctions();

    /**
     * Creates an instance of the Worker plugin, and uses it to execute a single job
     *
     * @param GearmanJob $job Gearman Job object, describing the work to be done
     */
    public static function rip(GearmanJob $job);

}

?>