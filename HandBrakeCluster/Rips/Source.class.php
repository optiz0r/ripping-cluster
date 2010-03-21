<?php

class HandBrakeCluster_Rips_Source {
    
    const PM_TITLE = 0;
    const PM_CHAPTER = 1;
    const PM_AUDIO = 2;
    const PM_SUBTITLE = 3;
    
    protected $source;
    protected $output;
    protected $titles = array();

    public function __construct($source_filename, $use_cache) {
        $this->source = $source_filename;

        $this->scan();

        $main   = HandBrakeCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();
        
        if ($use_cache) {
            $cache->store($this->source, serialize($this), $config->get('rips.cache_ttl'));
        }
    }
    
    public static function load($source_filename, $use_cache = true) {
        $cache = HandBrakeCluster_Main::instance()->cache();

        if ($use_cache && $cache->exists($source_filename)) {
            return unserialize($cache->fetch($source_filename));
        } else {
            return new HandBrakeCluster_Rips_Source($source_filename, $use_cache);
        }
    }

    protected function scan() {
        $source_shell = escapeshellarg($this->source);
        $handbrake_cmd = "HandBrakeCLI -i {$source_shell} -t 0";

        $handbrake_pid = proc_open($handbrake_cmd, array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
        ), $pipes);

        $handbrake_output = stream_get_contents($pipes[2]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($handbrake_pid);

        // Process the output
        $lines = explode("\n", $handbrake_output);
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
                    $title = new HandBrakeCluster_Rips_SourceTitle($matches['id']);
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

                case $title && $mode == self::PM_AUDIO && preg_match('/^    \+ (?P<id>\d+), (?P<name>.+) \((?P<format>.+)\) \((?P<channels>.+) ch\) \((?P<language>.+)\), (?P<samplerate>\d+)Hz, (?P<bitrate>\d+)bps$/', $line, $matches): {
                    $title->addAudioTrack(
                        new HandBrakeCluster_Rips_SourceAudioTrack(
                            $matches['id'], $matches['name'], $matches['format'], $matches['channels'],
                            $matches['language'], $matches['samplerate'], $matches['bitrate']
                        )
                    );
                } break;
                
                case $title && $mode == self::PM_SUBTITLE && preg_match('/^    \+ (?P<id>\d+), (?P<name>.+) \((?P<language>.+)\) \((?P<format>.+)\)$/', $line, $matches): {
                    $title->addSubtitleTrack(
                        new HandBrakeCluster_Rips_SourceSubtitleTrack(
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
        $main   = HandBrakeCluster_Main::instance();
        $cache  = $main->cache();
        $config = $main->config();

        return $cache->exists($source_filename, $config->get('rips.cache_ttl'));
    }
    
    public function addTitle(HandBrakeCluster_Rips_SourceTitle $title) {
        $this->titles[] = $title;
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
