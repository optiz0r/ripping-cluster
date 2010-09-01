<?php

class RippingCluster_Cache {
    
    protected $config;
    protected $cache_dir;
    
    public function __construct(RippingCluster_Config $config) {
        $this->config = $config;
        $this->cache_dir = $config->get('cache.base_dir');
        
        if (is_dir($this->cache_dir)) {
            if ( ! is_writeable($this->cache_dir)) {
                throw new RippingCluster_Exception_InvalidCacheDir();
            }
        } else {
            if ( ! RippingCluster_Main::mkdir_recursive($this->cache_dir)) {
                throw new RippingCluster_Exception_InvalidCacheDir();
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
            // Delete the cached item
            unlink($cache_filename);
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
            throw new RippingCluster_Exception_CacheObjectNotFound($source_filename);
        }
        
        return file_get_contents($cache_filename);
    }
    
};

?>