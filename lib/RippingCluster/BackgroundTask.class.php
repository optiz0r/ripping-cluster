<?php

class RippingCluster_BackgroundTask {

    protected function __construct() {
        
    }

    public static function run($command) {
        $pipes = array();
        $pid = proc_open($command . ' &', array(), $pipes);
        proc_close($pid);
    }
    
};

?>