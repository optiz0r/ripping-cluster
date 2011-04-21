<?php

class RippingCluster_Source_Plugin_MkvInfo extends RippingCluster_PluginBase implements RippingCluster_Source_IPlugin {
    
    /**
     * Name of this plugin
     * @var string
     */
    const PLUGIN_NAME = 'MkvInfo';
    
    /**
     * Name of the config setting that stores the list of source directories for this pluing
     * @var string
     */
    const CONFIG_SOURCE_DIR = 'source.mkvinfo.dir';
    
    const PM_HEADERS  = 0;
    const PM_TRACK    = 1;
    const PM_TITLE    = 2;
    const PM_CHAPTER  = 3;
    const PM_AUDIO    = 4;
    const PM_SUBTITLE = 5;
    
    /**
     * Returns a list of all Sources discovered by this plugin.
     * 
     * The sources are not scanned until specifically requested.
     * 
     * @return array(RippingCluster_Source)
     */
    public static function enumerate() {
        $config = RippingCluster_Main::instance()->config();
        $directories = $config->get(self::CONFIG_SOURCE_DIR);
        
        $sources = array();
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                throw new RippingCluster_Exception_InvalidSourceDirectory($directory);
            }
            
            $iterator = new RippingCluster_Utility_MkvFileIterator(new RecursiveIteratorIterator(new RippingCluster_Utility_VisibleFilesRecursiveIterator(new RecursiveDirectoryIterator($directory))));
            foreach ($iterator as /** @var SplFileInfo */ $source_mkv) {
                $sources[] = self::load($source_mkv->getPathname(), false);
            }
        }
        
        return $sources;
    }
    
    /**
     * Creates an object to represent the given source.
     * 
     * The source is not actually scanned unless specifically requested.
     * An unscanned object cannot be used until it has been manually scanned.
     * 
     * If requested, the source can be cached to prevent high load, and long scan times.
     * 
     * @param string $source_filename Filename of the source
     * @param bool $scan Request that the source be scanned for content. Defaults to true.
     * @param bool $use_cache Request that the cache be used. Defaults to true.
     * @return RippingCluster_Source
     */
    public static function load($source_filename, $scan = true, $use_cache = true) {
        $cache = RippingCluster_Main::instance()->cache();
        $config = RippingCluster_Main::instance()->config();

        // Ensure the source is a valid directory, and lies below the configured source_dir
        if ( ! self::isValidSource($source_filename)) {
            return new RippingCluster_Source($source_filename, self::name(), false);
        }
            
        $source = null;
        if ($use_cache && $cache->exists($source_filename)) {
            $source = unserialize($cache->fetch($source_filename));
        } else {
            $source = new RippingCluster_Source($source_filename, self::name(), true);
           
            if ($scan) {
                $cmd = escapeshellcmd($config->get('source.mkvinfo.bin')) . ' ' . escapeshellarg($source_filename);
                list($retval, $output, $error) = RippingCluster_ForegroundTask::execute($cmd);
                
                // Process the output
                $lines = explode("\n", $output);
                $track = null;
                $track_details = null;
                $duration = null;
                $mode = self::PM_HEADERS;
                
                foreach ($lines as $line) {
                    // Skip any line that doesn't begin with a |+ (with optional whitespace)
                    if ( ! preg_match('/^|\s*\+/', $line)) {
                        continue;
                    }
        
                    $matches = array();
                    switch (true) {
                        
                        case $mode == self::PM_HEADERS && preg_match('/^| \+ Duration: [\d\.]+s ([\d:]+])$/', $line, $matches): {
                            $duration = $matches['duration'];
                        } break;
                        
                        case preg_match('/^| \+ A track$/', $line, $matches): {
                            $mode = self::PM_TRACK;
                            $track_details = array();
                        } break;
                        
                        case $mode == self::PM_TRACK && preg_match('/^|  \+ Track number: (?P<id>\d+):$/', $line, $matches): {
                            $track_details['id'] = $matches['id'];
                        } break;
                        
                        case $mode == self::PM_TRACK && preg_match('/^|  \+ Track type: (?P<type>.+)$/', $line, $matches): {
                            switch ($type) {
                                case 'video': {
                                    $mode = self::PM_TITLE;
                                    $track = new RippingCluster_Rips_SourceTitle($track_details['id']);
                                    $track->setDuration($duration);
                                } break;
                                
                                case 'audio': {
                                    $mode = self::PM_AUDIO;
                                    $track = new RippingCluster_Rips_SourceAudioTrack($track_details['id']);
                                } break;
                                
                                case 'subtitles': {
                                    $mode = self::PM_SUBTITLE;
                                    $track = new RippingCluster_Rips_SourceSubtitleTrack($track_details['id']);
                                } break;
                            }
                        } break;
                        
                        case $mode == self::PM_AUDIO && $track && preg_match('/^|  \+ Codec ID: (?P<codec>.+)$/', $line, $matches): {
                            $track->setFormat($matches['codec']);
                        } break;
                        
                        case $mode == self::PM_AUDIO && $track && preg_match('/^|  \+ Language: (?P<language>.+)$/', $line, $matches): {
                            $track->setLanguage($matches['language']);
                        } break;
                        
                        case $mode == self::PM_AUDIO && $track && preg_match('/^|   \+ Sampling frequency: (?P<samplerate>.+)$/', $line, $matches): {
                            $track->setSampleRate($matches['samplerate']);
                        } break;
                        
                        case $mode == self::PM_AUDIO && $track && preg_match('/^|   \+ Channels: (?P<channels>.+)$/', $line, $matches): {
                            $track->setFormat($matches['channels']);
                        } break;
                        
                        case $mode == self::PM_SUBTITLE && $track && preg_match('/^|  \+ Language: (?P<language>.*)$/', $line): {
                            $track->setLanguage($matches['language']);
                        } break;
                        
                        case $mode == self::PM_TITLE && $track && preg_match('/^  \+ Default duration: [\d\.]+ \((?P<framerate>[\d\.]+ fps for a video track)\)$/', $line, $matches): {
                            $title->setFramerate($matches['framerate']);
                        } break;
                        
                        case $mode == self::PM_TITLE && $track && preg_match('/^   \+ Pixel width: (?P<width>\d+)$/', $line, $matches): {
                            $title->setWidth($matches['width']);
                        } break;
                        
                        case $mode == self::PM_TITLE && $track && preg_match('/^   \+ Pixel height: (?P<height>\d+)$/', $line, $matches): {
                            $title->setHeight($matches['height']);
                        } break;
                        
                        case $title && $mode == self::PM_CHAPTER && preg_match('/^    \+ (?P<id>\d+): cells \d+->\d+, \d+ blocks, duration (?P<duration>\d+:\d+:\d+)$/', $line, $matches): {
                            $title->addChapter($matches['id'], $matches['duration']);
                        } break;
        
                        case $title && $mode == self::PM_AUDIO && preg_match('/^    \+ (?P<id>\d+), (?P<name>.+) \((?P<format>.+)\) \((?P<channels>(.+ ch|Dolby Surround))\) \((?P<language>.+)\), (?P<samplerate>\d+)Hz, (?P<bitrate>\d+)bps$/', $line, $matches): {
                            $title->addAudioTrack(
                                new RippingCluster_Rips_SourceAudioTrack(
                                    $matches['id'], $matches['name'], $matches['format'], $matches['channels'],
                                    $matches['language'], $matches['samplerate'], $matches['bitrate']
                                )
                            );
                        } break;
                        
                        case $title && $mode == self::PM_SUBTITLE && preg_match('/^    \+ (?P<id>\d+), (?P<name>.+) \((?P<language>.+)\) \((?P<format>.+)\)$/', $line, $matches): {
                            $title->addSubtitleTrack(
                                new RippingCluster_Rips_SourceSubtitleTrack(
                                    $matches['id'], $matches['name'], $matches['language'], $matches['format']
                                )
                            );
                        } break;
                        
                        default: {
                            // Ignore this unmatched line
                        } break;
        
                    }
                }
                
            }
            
            // If requested, store the new source object in the cache
            if ($use_cache) {
                $source->cache();
            }
        }
    }
    
    /**
     * Creates an object to represent the given source using an encoded filename.
     * 
     * Wraps the call to load the source after the filename has been decoded.
     * 
     * @param string $encoded_filename Encoded filename of the source
     * @param bool  $scan Request that the source be scanned for content. Defaults to true.
     * @param bool $use_cache Request that the cache be used. Defaults to true.
     * @return RippingCluster_Source
     * 
     * @see RippingCluster_Source_IPlugin::load()
     */
    public static function loadEncoded($encoded_filename, $scan = true, $use_cache = true) {
        // Decode the filename
        $source_filename = base64_decode(str_replace('-', '/', $encoded_filename));

        return self::load($source_filename, $scan, $use_cache);
    }
    
    /**
     * Determins if a filename is a valid source loadable using this plugin
     * 
     * @param string $source_filename Filename of the source
     * @return bool
     */
    public static function isValidSource($source_filename) {
        $config = RippingCluster_Main::instance()->config();
        
        // Ensure the source is a valid directory, and lies below the configured source_dir
        if ( ! is_dir($source_filename)) {
            return false;
        }
        $real_source_filename = realpath($source_filename);
        
            // Check all of the source directories specified in the config
        $source_directories = $config->get(self::CONFIG_SOURCE_DIR);
        foreach ($source_directories as $source_basedir) { 
            $real_source_basedir = realpath($source_basedir);
            
            if (substr($real_source_filename, 0, strlen($real_source_basedir)) != $real_source_basedir) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Permanently deletes the given source from disk
     * 
     * @param RippingCluster_Source $source Source object to be deleted
     * @return bool
     */
    public static function delete($source_filename) {
        if ( ! self::isValidSource($source_filename)) {
            return false;
        }
        
        return unlink($source_filename);
    }
    
}

?>