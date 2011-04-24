<?php

class Net_Gearman_Job_HandBrake extends Net_Gearman_Job_Common implements RippingCluster_Worker_IPlugin {

    const DEINTERLACE_ALWAYS      = 1;
    const DEINTERLACE_SELECTIVELY = 2;
    
    private $output;
    
    private $job;
    
    public function __construct($conn, $handle) {
        parent::__construct($conn, $handle);
        
        $this->output = '';
    }
    
    public static function init() {
        
    }
    
    public static function name() {
        
    }
    
    public function run($args) {;
        $main   = RippingCluster_Main::instance();
        $config = $main->config();
        $log    = $main->log();
        
        $this->job = RippingCluster_Job::fromId($args['rip_options']['id']);
        
        $handbrake_cmd_raw = array(
            '-n', $config->get('rips.nice'),
            $config->get('rips.handbrake_binary'),
            self::evaluateOption($args['rip_options'], 'input_filename', '-i'),
            self::evaluateOption($args['rip_options'], 'output_filename', '-o'),
            self::evaluateOption($args['rip_options'], 'title'),
            self::evaluateOption($args['rip_options'], 'format', '-f'),
            self::evaluateOption($args['rip_options'], 'video_codec', '-e'),
            self::evaluateOption($args['rip_options'], 'quantizer', '-q'),
            self::evaluateOption($args['rip_options'], 'video_width', '-w'),
            self::evaluateOption($args['rip_options'], 'video_height', '-l'),
            self::evaluateOption($args['rip_options'], 'deinterlace'),
            self::evaluateOption($args['rip_options'], 'audio_tracks', '-a'),
            self::evaluateOption($args['rip_options'], 'audio_codec', '-E'),
            self::evaluateOption($args['rip_options'], 'audio_names', '-A'),
            self::evaluateOption($args['rip_options'], 'subtitle_tracks', '-s'),
        );

        $handbrake_cmd = array($config->get('rips.nice_binary'));
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($handbrake_cmd_raw)) as $value) {
            $handbrake_cmd[] = escapeshellarg($value);
        }
        $handbrake_cmd = join(' ', $handbrake_cmd);
        RippingCluster_WorkerLogEntry::debug($log, $this->job->id(), $handbrake_cmd);
        
        // Change the status of this job to running
        RippingCluster_WorkerLogEntry::debug($log, $this->job->id(), "Setting status to Running");
        $this->job->updateStatus(RippingCluster_JobStatus::RUNNING, 0);

        list($return_val, $stdout, $stderr) = RippingCluster_ForegroundTask::execute($handbrake_cmd, null, null, null, array($this, 'callbackOutput'), array($this, 'callbackOutput'), $this);
        if ($return_val) {
            $this->fail($return_val);
        } else {
            $this->job->updateStatus(RippingCluster_JobStatus::COMPLETE);
            $this->complete( array(
                'id' => $this->job->id()
            ));
        }
    }
    
    private static function evaluateOption($options, $name, $option = null) {
        switch($name) {
            case 'title': {
                if (!$options[$name] || (int)$options[$name] < 0) {
                    return array('-L');
                } else {
                    return array('-t', $options[$name]);
                }
            } break;
            
            case 'deinterlace': {
                switch ($options[$name]) {
                    case self::DEINTERLACE_ALWAYS:
                        return array('-d');
                    case self::DEINTERLACE_SELECTIVELY:
                        return array('-5');
                    default:
                        return array();
                }
            }
            
            default:
                return array(isset($option) ? $option : $name, $options[$name]);
        } 
    }
    
    public function callbackOutput($rip, $data) {
		$this->output .= $data;

        while (count($lines = preg_split('/[\r\n]+/', $this->output, 2)) > 1) {
            $line = $lines[0];
            $rip->output = $lines[1];
            
            $matches = array();
            if (preg_match('/Encoding: task \d+ of \d+, (\d+\.\d+) %/', $line, $matches)) {
                $status = $rip->job->currentStatus();
                $status->updateRipProgress($matches[1]);
                $this->status($matches[1], 100);
            } else {
                $log = RippingCluster_Main::instance()->log();
                RippingCluster_WorkerLogEntry::debug($log, $rip->job->id(), $line);
            }
        }
    }
    
}

?>
