<?php

class RippingCluster_Utility_DvdDirectoryIterator extends FilterIterator {
    public function accept() {
        return true; // TODO Determine if the current directory item represents a Bluray source or not
    }
}

?>