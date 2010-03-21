<?php

class HandBrakeCluster_Cache {
    
    protected $config;
    protected $cache_dir;
    
    public function __construct(HandBrakeCluster_Config $config) {
        $this->config = $config;
        $this->cache_dir = $config->get('cache.base_dir');
        
        if (is_dir($cache_dir)) {
            if ( ! is_writeable($cache_dir)) {
                throw new HandBrakeCluster_Exception_InvalidCacheDir();
            }
        } else {
            if ( ! HandBrakeCluster_Main::mkdir_recursive($this->cache_dir)) {
                throw new HandBrakeCluster_Exception_InvalidCacheDir();
            }
        }
    }
    
    protected function cacheFilename($source_filename) {
        return $this->cache_dir . sha1($source_filename);
    }
    
    public function exists($source_filename, $ttl = 3600) {
        $cache_filename = $this->cacheFilename($source_filename);
        
        // Check to see if the file is cached
        if (!file_exists($cache_filename)) {
            return false;
        }
        
        // Check to see if the cache has expired
        if (filemtime($cache_filename) + $ttl < time()) {
            return false;
        }
        
        return true;
    }
    
    public function store($source_filename, $content) {
        $cache_filename = $this->cacheFilename($source_filename);
        return file_put_contents($cache_filename, $content);
    }
    
    public function fetch($source_filename, $ttl = 3600) {
        $cache_filename = $this->cacheFilename($source_filename);
        
        if (!$this->exists($source_filename)) {
            throw new HandBrakeCluster_Exception_CacheObjectNotFound($source_filename);
        }
        
        return file_get_contents($cache_filename);
    }
    
};

?>