<?php

class HandBrakeCluster_Job {

    private $id;

    public function __construct() {
        $this->id = 42;
    }

    public function id() {
        return $this->id;
    }

};

?>
