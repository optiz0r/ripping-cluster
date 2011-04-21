<?php

class RippingCluster_Utility_BlurayDirectoryIterator extends FilterIterator {
    public function accept() {
        return is_dir($this->current()->getPathname() . DIRECTORY_SEPARATOR . 'BDAV') ||
               is_dir($this->current()->getPathname() . DIRECTORY_SEPARATOR . 'BDMV');
    }
}

?>