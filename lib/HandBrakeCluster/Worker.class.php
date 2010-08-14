<?php

class HandBrakeCluster_Worker {
    
    const DEINTERLACE_ALWAYS      = 1;
    const DEINTERLACE_SELECTIVELY = 2;
    
    public function __construct() {
        $gearman = new GearmanWorker();
        $gearman->addServers($config->get('rips.job_servers'));
        $gearman->addFunction('handbrake_rip', 'hbc_gearman_handbrake_rip');
    }
    
    public function start() {
        while($gearman->work()) {
            if ($gearman->returnCode() != GEARMAN_SUCCESS) {
                break;
            }
        }
        
        return true;
    }
    
    public function handbrakeRip($client_job_id, $rip_options) {
        $handbrake_cmd_raw = array(
            '-n', $config->get('rips.nice'),
            $config->get('rips.handbrake_binary'),
            self::evaluateOption($rip_options, 'input_filename', '-i'),
            self::evaluateOption($rip_options, 'real_output_filename', '-o'),
            self::evaluateOption($rip_options, 'title'),
            self::evaluateOption($rip_options, 'format', '-f'),
            self::evaluateOption($rip_options, 'video_codec', '-e'),
            self::evaluateOption($rip_options, 'quantizer', '-q'),
            self::evaluateOption($rip_options, 'video_width', '-w'),
            self::evaluateOption($rip_options, 'video_height', '-l'),
            self::evaluateOption($rip_options, 'deinterlace'),
            self::evaluateOption($rip_options, 'audio_tracks', '-a'),
            self::evaluateOption($rip_options, 'audio_codec', '-E'),
            self::evaluateOption($rip_options, 'audio_names', '-A'),
            self::evaluateOption($rip_options, 'subtitle_tracks', '-s'),   
        );
        
        $handbrake_cmd = array($config->get('rips.nice_binary'));
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($handbrake_cmd_raw)) as $value) {
            $handbrake_cmd[] = shell_escape_arg($value);
        }
        $handbrake_cmd = array_join(' ', $handbrake_cmd);

        return HandBrakeCluster_ForegroundTask::execute($handbrake_cmd, null, null, null, array($this, 'callback_stdout'), array($this, 'callback_stderr'), $this);

    } 
    
    protected static function evaluateOption(array &$rip_options, $name, $option = null) {
        switch($name) {
            case 'title': {
                if (!$rip_options[$name] || int($rip_options[$name]) < 0) {
                    return array('-L');
                } else {
                    return array('-t', $rip_options[$name]);
                }
            } break;
            
            case 'deinterlace': {
                switch ($rip_options[$name]) {
                    case self::DEINTERLACE_ALWAYS:
                        return array('-d');
                    case self::DEINTERLACE_SELECTIVELY:
                        return array('-5');
                    default:
                        return array();
                }
            }
            
            default:
                return array(isset($option) ? $option : $name, $rip_options[$name]);
        } 
    }
    
}

function hbc_gearman_handbrake_rip(GearmanJob $job) {
    return $worker->handbrakeRip($job->unique(), unserialize($job->workload));
}

?>