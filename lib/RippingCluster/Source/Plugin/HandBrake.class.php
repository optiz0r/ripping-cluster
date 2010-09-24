<?php

class RippingCluster_Source_Plugin_HandBrake extends RippingCluster_PluginBase implements RippingCluster_Source_IPlugin {
    
    /**
     * Name of this plugin
     *
     * @var string
     */
    const PLUGIN_NAME = "HandBrake";
    
    const PM_TITLE    = 0;
    const PM_CHAPTER  = 1;
    const PM_AUDIO    = 2;
    const PM_SUBTITLE = 3;
    
    /**
     * Returns a list of all Sources discovered by this plugin.
     * 
     * The sources are not scanned until specifically requested.
     * 
     * @return array(RippingCluster_Source)
     */
    public static function enumerate() {
        $config = RippingCluster_Main::instance()->config();
        $directories = $config->get('source.handbrake.dir');
        
        $sources = array();
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                throw new RippingCluster_Exception_InvalidSourceDirectory($directory);
            }
            
            $iterator = new RippingCluster_Utility_DvdDirectoryIterator(new RippingCluster_Utility_VisibleFilesIterator(new DirectoryIterator($directory)));
            foreach ($iterator as /** @var SplFileInfo */ $source_vts) {
                $sources[] = self::load($source_vts->getPathname(), false);
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
                $source_shell = escapeshellarg($source_filename);
                $handbrake_cmd = "HandBrakeCLI -i {$source_shell} -t 0";
                list($retval, $handbrake_output, $handbrake_error) = RippingCluster_ForegroundTask::execute($handbrake_cmd);
                
                // Process the output
                $lines = explode("\n", $handbrake_error);
                $title = null;
                $mode = self::PM_TITLE;
        
                foreach ($lines as $line) {
                    // Skip any line that doesn't begin with a + (with optional leading whitespace)
                    if ( ! preg_match('/\s*\+/', $line)) {
                        continue;
                    }
        
                    $matches = array();
                    switch (true) {
                       case preg_match('/^\+ title (?P<id>\d+):$/', $line, $matches): {
                            if ($title) {
                                $source->addTitle($title);
                            }
                            
                            $mode = self::PM_TITLE;
                            $title = new RippingCluster_Rips_SourceTitle($matches['id']);
                        } break;
                        
                        case $title && preg_match('/^  \+ chapters:$/', $line): {
                            $mode = self::PM_CHAPTER;    
                        } break;
                        
                        case $title && preg_match('/^  \+ audio tracks:$/', $line): {
                            $mode = self::PM_AUDIO;
                            
                        } break;
                        
                        case $title && preg_match('/^  \+ subtitle tracks:$/', $line): {
                            $mode = self::PM_SUBTITLE;
                        } break;
                        
                        case $title && $mode == self::PM_TITLE && preg_match('/^  \+ duration: (?P<duration>\d+:\d+:\d+)$/', $line, $matches): {
                            $title->setDuration($matches['duration']);
                        } break;
                        
                        case $title && $mode == self::PM_TITLE && preg_match('/^  \+ angle\(s\) (?P<angles>\d+)$/', $line, $matches): {
                            $title->setAngles($matches['angles']);
                        } break;
                        
                        //"  + size: 720x576, pixel aspect: 64/45, display aspect: 1.78, 25.000 fps"
                        case $title && $mode == self::PM_TITLE && preg_match('/^  \+ size: (?P<width>\d+)x(?P<height>\d+), pixel aspect: (?P<pixel_aspect>\d+\/\d+), display aspect: (?P<display_aspect>[\d\.]+), (?<framerate>[\d\.]+) fps$/', $line, $matches): {
                            $title->setDisplayInfo(
                                $matches['width'], $matches['height'], $matches['pixel_aspect'],
                                $matches['display_aspect'], $matches['framerate']
                            );
                        } break;
                        
                        case $title && $mode == self::PM_TITLE && preg_match('/^  \+ autocrop: (?P<autocrop>(?:\d+\/?){4})$/', $line, $matches): {
                            $title->setAutocrop($matches['autocrop']);
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
                
                // Handle the last title found as a special case
                if ($title) {
                    $source->addTitle($title);
                }
    
                // If requested, store the new source object in the cache
                if ($use_cache) {
                    $source->cache();
                }
            }
        }

        return $source;
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
        $source_directories = $config->get('source.handbrake.dir');
        foreach ($source_directories as $source_basedir) {
            $real_source_basedir = realpath($source_basedir);
            
            if (substr($real_source_filename, 0, strlen($real_source_basedir)) == $real_source_basedir) {
                return true;
            }
        }
        
        return false;
    }
    
}

?>