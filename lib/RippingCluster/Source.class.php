<?php

class RippingCluster_Source {
    
    const PM_TITLE = 0;
    const PM_CHAPTER = 1;
    const PM_AUDIO = 2;
    const PM_SUBTITLE = 3;
    
    protected $exists;
    protected $filename;
    protected $plugin;
    protected $titles = array();

    public function __construct($source_filename, $plugin, $exists) {
        $this->exists   = $exists;
        $this->filename = $source_filename;
        $this->plugin   = $plugin;
    }
    
    public static function isCached($source_filename) {
        $main   = RippingCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();

        return $cache->exists($source_filename, $config->get('rips.cache_ttl'));
    }
    
    public function cache() {
        if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        $main   = RippingCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();
        
        $cache->store($this->filename, serialize($this), $config->get('rips.cache_ttl'));
    }
    
    public static function encodeFilename($filename) {
        return str_replace("/", "-", base64_encode($filename));
    }
    
    public function addTitle(RippingCluster_Rips_SourceTitle $title) {
        if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        $this->titles[] = $title;
    }
    
	public function longestTitle() {
	    if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        $longest_title = null;
	    $maximum_duration = 0;
	    
	    if ( ! $this->titles) {
	        return null;
	    }
	    
	    foreach ($this->titles as $title) {
	        $duration = $title->durationInSeconds();
	        if ($duration > $maximum_duration) {
	            $longest_title = $title;
	            $maximum_duration = $duration;
	        }
	    }
	    
	    return $longest_title;
    }

    public function longestTitleIndex() {
        if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        $longest_index = null;
        $maximmum_duration = 0;

        if ( ! $this->titles) {
            return null;
        }

        for ($i = 0, $l = count($this->titles); $i < $l; ++$i) {
            $title = $this->titles[$i];
            $duration = $title->durationInSeconds();
            if ($duration > $maximum_duration) {
                $longest_index = $i;
                $maximum_duration = $duration;
            }
        }

        return $longest_index;
    }
	
    public function filename() {
        return $this->filename;
    }
    
    public function filenameEncoded() {
        return self::encodeFilename($this->filename);
    }
    
    public function plugin() {
        return $this->plugin;
    }

    public function titleCount() {
        if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        return count($this->titles);
    }

    public function titles() {
        if (!$this->exists) {
            throw new RippingCluster_Exception_InvalidSourceDirectory();
        }
        
        return $this->titles;
    }

};

?>
