<?php

class HandBrakeCluster_Rips_Source {

    protected $source;
    protected $output;
    
    public function __construct($source) {
        $this->source = $source;

        $this->scan();
    }
    
    protected function scan() {
        $source_shell = escapeshellarg($this->source);
        $handbrake_cmd = "HandBrakeCLI -i {$source_shell} -t 0"; 
        
        $handbrake_pid = popen($handbrake_cmd, 'r');
        $handbrake_output = fread($handbrake_pid, 1024);
        while (!feof($handbrake_pid)) {
            $handbrake_output = fread($handbrake_pid, 1024);
        }
        pclose($handbrake_pid);
        
        
        // Process the output
        $lines = explode("\n", $handbrake_output);
        foreach ($lines as $line) {
            // Skip any line that doesn't begin with a + (with optional leading whitespace)
            if ( ! preg_match('/\s*\+/', $line)) {
                continue;
            }
            
            $this->output .= $line;
        }
    }
    
    public function output() {
        return $output;
    }
};

?>