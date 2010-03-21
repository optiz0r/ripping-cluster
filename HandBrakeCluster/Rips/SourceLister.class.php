<?php

class HandBrakeCluster_Rips_SourceLister {
    
    protected $base_directory;
    protected $sources = array();

    public function __construct($base_directory) {
        $this->base_directory = $base_directory;
        
        $this->scan();
    }
    
    public function scan() {
        if (!is_dir($this->base_directory)) {
            throw new HandBrakeCluster_Exception_InvalidSourceDir($this->base_directory);
        }
        
        // Define a queue of directories to scan, starting with the base directory,
        // and keep going until they have all been scanned
        $scan_directories = array($this->base_directory);
        while ($scan_directories) {
            $dir = dir(array_shift($scan_directories));
            
            while (($entry = $dir->read()) !== false) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                
                // Skip any non-directories
                $source = $dir->path . DIRECTORY_SEPARATOR . $entry;
                if (!is_dir($source)) {
                    continue;
                }
                
                // Accept this dir as a source if it contains a VIDEO_TS dir,
                // otherwise add the dir to the queue to scan deeper
                $source_vts = $source . DIRECTORY_SEPARATOR . 'VIDEO_TS';
                if (is_dir($source_vts)) {
                    $this->sources[] = $source_vts;
                } else {
                    $scan_directories[] = $source;
                }
            }
            
            $dir->close();
        }
    }
    
    public function sources() {
        return $this->sources;
    }
    
};

?>