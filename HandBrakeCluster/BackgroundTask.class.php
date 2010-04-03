<?php

class HandBrakeCluster_BackgroundTask {

    protected function __construct() {
        
    }

    public function run($command) {
        $pipes = array();
        $pid = proc_open($command . ' &', array(), $pipes);
        proc_close($pid);
    }
    
};

?>