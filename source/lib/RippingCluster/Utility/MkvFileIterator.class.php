<?php

class RippingCluster_Utility_MkvFileIterator extends FilterIterator {
    public function accept() {
        return preg_match('/\.mkv$/i', $this->current()->getFilename());
    }
}

?>