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
        foreach ($lines as $line) {
            // Skip any line that doesn't begin with a + (with optional leading whitespace)
            if ( ! preg_match('/\s*\+/', $line)) {
                continue;
            }
            
            $this->output .= $line . "\n";
        }
    }
    
    public function output() {
        return $this->output;
    }
};

?>
