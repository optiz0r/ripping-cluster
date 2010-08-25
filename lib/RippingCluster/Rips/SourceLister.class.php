<?php

class RippingCluster_Rips_SourceLister {
    
    protected $base_directory;
    protected $sources = array();

    public function __construct($base_directory) {
        $this->base_directory = $base_directory;
        
        $this->scan();
    }
    
    public function scan() {
        if (!is_dir($this->base_directory)) {
            throw new RippingCluster_Exception_InvalidSourceDirectory($this->base_directory);
        }
        
        $iterator = new RippingCluster_Utility_DvdDirectoryIterator(new RippingCluster_Utility_VisibleFilesIterator(new DirectoryIterator($this->base_directory)));
        foreach ($iterator as /** @var SplFileInfo */ $source_vts) {
            $this->sources[] = RippingCluster_Rips_Source::load($source_vts->getPathname(), false);
        }
    }
    
    public function sources() {
        return $this->sources;
    }
    
};

?>
