<?php

interface RippingCluster_Worker_IPlugin extends RippingCluster_IPlugin {
    
    public static function workerFunctions();

    public static function rip(GearmanJob $job);

}

?>