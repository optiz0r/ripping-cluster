<?php

class HandBrakeCluster_Utility_DvdDirectoryIterator extends FilterIterator {
    public function accept() {
        return is_dir($this->current()->getPathname() . DIRECTORY_SEPARATOR . 'VIDEO_TS');
    }
}

?>