<?php

class RippingCluster_Utility_VisibleFilesIterator extends FilterIterator {
    public function accept() {
        return !(substr($this->current()->getFilename(), 0, 1) == '.');
    }
}

?>