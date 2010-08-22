<?php

interface HandBrakeCluster_Worker_IPlugin {
    
    public static function workerFunctions();

    public static function rip(GearmanJob $job);
    
    public function evaluateOption($name, $option = null);
    
    public function callbackStdout($id, $data);
    
    public function callbackStderr($id, $data);
}

?>