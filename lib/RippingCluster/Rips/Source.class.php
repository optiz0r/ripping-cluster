<?php

class RippingCluster_Rips_Source {
    
    const PM_TITLE = 0;
    const PM_CHAPTER = 1;
    const PM_AUDIO = 2;
    const PM_SUBTITLE = 3;
    
    protected $source;
    protected $output;
    protected $titles = array();

    protected function __construct($source_filename, $scan_dir, $use_cache) {
        $this->source = $source_filename;
        
        if ($scan_dir) {
            $this->scan();
        }

        $main   = RippingCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();
        
        if ($scan_dir && $use_cache) {
            $cache->store($this->source, serialize($this), $config->get('rips.cache_ttl'));
        }
    }
    
    public static function load($source_filename, $scan_dir = true, $use_cache = true) {
        $cache = RippingCluster_Main::instance()->cache();

        if ($use_cache && $cache->exists($source_filename)) {
            return unserialize($cache->fetch($source_filename));
        } else {
            return new RippingCluster_Rips_Source($source_filename, $scan_dir, $use_cache);
        }
    }
    
    public static function loadEncoded($encoded_filename, $scan_dir = true, $use_cache = true) {
        // Decode the filename
        $source_filename = base64_decode(str_replace('-', '/', $encoded_filename));

        // Ensure the source is a valid directory, and lies below the configured source_dir
        $real_source_filename = realpath($source_filename);
        if (!is_dir($source_filename)) {
            throw new RippingCluster_Exception_InvalidSourceDirectory($source_filename);
        }
        
        $config = RippingCluster_Main::instance()->config();
        $source_basedir = $config->get('rips.source_dir');
        $real_source_basedir = realpath($source_basedir);
        if (substr($real_source_filename, 0, strlen($real_source_basedir)) != $real_source_basedir) {
            throw new RippingCluster_Exception_InvalidSourceDirectory($source_filename);
        }
                
        return self::load($source_filename, $scan_dir, $use_cache);
    }

    protected function scan() {
        $source_shell = escapeshellarg($this->source);
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
                        $this->addTitle($title);
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

            $this->output .= $line . "\n";
        }
        
        // Handle the last title found as a special case
        if ($title) {
            $this->addTitle($title);
        }
    }
    
    public static function isCached($source_filename) {
        $main   = RippingCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();

        return $cache->exists($source_filename, $config->get('rips.cache_ttl'));
    }
    
    public static function encodeFilename($filename) {
        return str_replace("/", "-", base64_encode($filename));
    }
    
    public function addTitle(RippingCluster_Rips_SourceTitle $title) {
        $this->titles[] = $title;
    }
    
	public function longestTitle() {
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
        return $this->source;
    }
    
    public function filenameEncoded() {
        return self::encodeFilename($this->source);
    }

    public function output() {
        return $this->output;
    }
    
    public function titleCount() {
        return count($this->titles);
    }

    public function titles() {
        return $this->titles;
    }

};

?>
