<?php

class HandBrakeCluster_Job {

    private $id;

    public function __construct($id) {
        $this->id = $id;
    }

    public function id() {
        return $this->id;
    }

};

?>
