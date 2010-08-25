<?php

class RippingCluster_ForegroundTask {
    
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
        $process = proc_open($command, $descriptors, $pipes);
        
        stream_set_blocking($pipes[self::PIPE_STDIN], 0); // Make stdin/stdout/stderr non-blocking
        stream_set_blocking($pipes[self::PIPE_STDOUT], 0);
        stream_set_blocking($pipes[self::PIPE_STDERR], 0);
        
        if ($txLen == 0) {
            fclose($pipes[0]);
        }
        
        while (true) {
            $rx = array(); // The program's stdout/stderr
            if (!$stdoutDone) {
                $rx[] = $pipes[self::PIPE_STDOUT];
            }
            if (!$stderrDone) {
                $rx[] = $pipes[self::PIPE_STDERR];
            }
            
            $tx = array(); // The program's stdin
            if ($txOff < $txLen) {
                $tx[] = $pipes[self::PIPE_STDIN];
            }
            
            stream_select($rx, $tx, $ex = null, null, null); // Block til r/w possible
            if (!empty($tx)) {
                $txRet = fwrite($pipes[self::PIPE_STDIN], substr($stdin, $txOff, 8192));
                if ($txRet !== false) {
                    $txOff += $txRet;
                }
                if ($txOff >= $txLen) {
                    fclose($pipes[self::PIPE_STDIN]);
                }
            }
            
            foreach ($rx as $r) {
                if ($r == $pipes[self::PIPE_STDOUT]) {
                    $chunk = fread($pipes[self::PIPE_STDOUT], 8192);
                    if (feof($pipes[self::PIPE_STDOUT])) { 
                        fclose($pipes[self::PIPE_STDOUT]); $stdoutDone = true;
                    }
                    
                    if ($callback_stdout) {
                        call_user_func($callback_stdout, $identifier, $chunk);
                    } else {
                        $stdout .= $chunk;
                    }
                    
                } else if ($r == $pipes[self::PIPE_STDERR]) {
                    $chunk = fread($pipes[self::PIPE_STDERR], 8192);
                    if (feof($pipes[self::PIPE_STDERR])) {
                        fclose($pipes[self::PIPE_STDERR]); $stderrDone = true;
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

