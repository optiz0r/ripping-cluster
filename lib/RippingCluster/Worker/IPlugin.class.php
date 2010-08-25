<?php

interface RippingCluster_Worker_IPlugin {
    
    public static function init();
    
    public static function name();
    
    public static function workerFunctions();

    public static function rip(GearmanJob $job);

}

?>