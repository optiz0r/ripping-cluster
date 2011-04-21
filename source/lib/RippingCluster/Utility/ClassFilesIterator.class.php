<?php

class RippingCluster_Utility_ClassFilesIterator extends FilterIterator {
    public function accept() {
        return preg_match('/.class.php$/i', $this->current()->getFilename());
    }
}

?>