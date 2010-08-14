<?php

class HandBrakeCluster_ForegroundTask {
    
    const PIPE_STDIN  = 0;
    const PIPE_STDOUT = 1;
    const PIPE_STDERR = 2;
    
    private function __construct() {
    
    }
    
    /**
     * 
     * Code largely taken from user submitted comment on http://php.sihnon.net/manual/en/function.proc-open.php
     * @param unknown_type $command
     * @param unknown_type $cwd
     * @param unknown_type $env
     * @param unknown_type $stdin
     * @param unknown_type $callback_stdout
     * @param unknown_type $callback_stderr
     */
    public static function execute($command, $cwd = null, $env = null, $stdin = null, $callback_stdout = null, $callback_stderr = null, $identifier = null) {
        $txOff = 0;
        $txLen = strlen($stdin);
        $stdout = '';
        $stdoutDone = FALSE;
        $stderr = '';
        $stderrDone = FALSE;
        
        $descriptors = array(
            self::PIPE_STDIN  => array('pipe', 'r'),
            self::PIPE_STDOUT => array('pipe', 'w'),
            self::PIPE_STDERR => array('pipe', 'w'),
        );
        
        $pipes = array();
        $process = proc_open($handbrake_cmd, $descriptors, $pipes);
        
        stream_set_blocking($pipes[0], 0); // Make stdin/stdout/stderr non-blocking
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        
        if ($txLen == 0) {
            fclose($pipes[0]);
        }
        
        while (true) {
            $rx = array(); // The program's stdout/stderr
            if (!$stdoutDone) {
                $rx[] = $pipes[1];
            }
            if (!$stderrDone) {
                $rx[] = $pipes[2];
            }
            
            $tx = array(); // The program's stdin
            if ($txOff < $txLen) {
                $tx[] = $pipes[0];
            }
            
            stream_select($rx, $tx, $ex = null, null, null); // Block til r/w possible
            if (!empty($tx)) {
                $txRet = fwrite($pipes[0], substr($stdin, $txOff, 8192));
                if ($txRet !== false) {
                    $txOff += $txRet;
                }
                if ($txOff >= $txLen) {
                    fclose($pipes[0]);
                }
            }
            
            foreach ($rx as $r) {
                if ($r == $pipes[1]) {
                    $chunk = fread($pipes[1], 8192);
                    if (feof($pipes[1])) { 
                        fclose($pipes[1]); $stdoutDone = true;
                    }
                    
                    if ($callback_stderr) {
                        call_user_func($callback_stdout, $identifier, $chunk);
                    } else {
                        $stdout .= $chunk;
                    }
                    
                } else if ($r == $pipes[2]) {
                    $chunk = fread($pipes[2], 8192);
                    if (feof($pipes[2])) {
                        fclose($pipes[2]); $stderrDone = true;
                    }
                    
                    if ($callback_stderr) {
                        call_user_func($callback_stderr, $identifier, $chunk);
                    } else {
                        $stderr .= $chunk;
                    }
                }
            }
            
            if (!is_resource($process))
                break;
                
            if ($txOff >= $txLen && $stdoutDone && $stderrDone)
                break;
        }
        
        return array(proc_close($process), $stdout, $stderr);
    }
    
}

