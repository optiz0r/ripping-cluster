<?php

class RippingCluster_Worker_Plugin_HandBrake extends RippingCluster_PluginBase implements RippingCluster_Worker_IPlugin {

    const PLUGIN_NAME = 'HandBrake';
    
    const DEINTERLACE_ALWAYS      = 1;
    const DEINTERLACE_SELECTIVELY = 2;
    
    private $output;
    
    private $job;
    
    private $rip_options;
    
    private function __construct(GearmanJob $gearman_job) {
        $this->output = '';
        
        $this->gearman_job = $gearman_job;
        
        $this->rip_options = unserialize($this->gearman_job->workload());

        if ( ! $this->rip_options['id']) {
            throw new RippingCluster_Exception_LogicException("Job ID must not be zero/null");
        }
        $this->job = RippingCluster_Job::fromId($this->rip_options['id']);
    }
    
    /**
     * Returns the list of functions (and names) implemented by this plugin for registration with Gearman
     * 
     * @return array(string => callback)
     */
    public static function workerFunctions() {
        return array(
            'handbrake_rip' => array(__CLASS__, 'rip'),
        );
    }
    
    /**
     * Creates an instance of the Worker plugin, and uses it to execute a single job
     *
     * @param GearmanJob $job Gearman Job object, describing the work to be done
     */
    public static function rip(GearmanJob $job) {
        $rip = new self($job);
        $rip->execute();
    }
        
    private function execute() {
        $main   = RippingCluster_Main::instance();
        $config = $main->config();
        $log    = $main->log();
        
        $handbrake_cmd_raw = array(
            '-n', $config->get('rips.nice'),
            $config->get('rips.handbrake_binary'),
            $this->evaluateOption('input_filename', '-i'),
            $this->evaluateOption('output_filename', '-o'),
            $this->evaluateOption('title'),
            $this->evaluateOption('format', '-f'),
            $this->evaluateOption('video_codec', '-e'),
            $this->evaluateOption('quantizer', '-q'),
            $this->evaluateOption('video_width', '-w'),
            $this->evaluateOption('video_height', '-l'),
            $this->evaluateOption('deinterlace'),
            $this->evaluateOption('audio_tracks', '-a'),
            $this->evaluateOption('audio_codec', '-E'),
            $this->evaluateOption('audio_names', '-A'),
            $this->evaluateOption('subtitle_tracks', '-s'),
        );

        $handbrake_cmd = array($config->get('rips.nice_binary'));
        foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($handbrake_cmd_raw)) as $value) {
            $handbrake_cmd[] = escapeshellarg($value);
        }
        $handbrake_cmd = join(' ', $handbrake_cmd);
        $log->debug($handbrake_cmd, $this->job->id());
        
        // Change the status of this job to running
        $log->debug("Setting status to Running", $this->job->id());
        $this->job->updateStatus(RippingCluster_JobStatus::RUNNING, 0);

        list($return_val, $stdout, $stderr) = RippingCluster_ForegroundTask::execute($handbrake_cmd, null, null, null, array($this, 'callbackOutput'), array($this, 'callbackOutput'), $this);
        if ($return_val) {
            $this->gearman_job->sendFail($return_val);
        } else {
            $this->job->updateStatus(RippingCluster_JobStatus::COMPLETE);
        }
    }
    
    private function evaluateOption($name, $option = null) {
        switch($name) {
            case 'title': {
                if (!$this->rip_options[$name] || (int)$this->rip_options[$name] < 0) {
                    return array('-L');
                } else {
                    return array('-t', $this->rip_options[$name]);
                }
            } break;
            
            case 'deinterlace': {
                switch ($this->rip_options[$name]) {
                    case self::DEINTERLACE_ALWAYS:
                        return array('-d');
                    case self::DEINTERLACE_SELECTIVELY:
                        return array('-5');
                    default:
                        return array();
                }
            }
            
            default:
                return array(isset($option) ? $option : $name, $this->rip_options[$name]);
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
            } else {
                $log = RippingCluster_Main::instance()->log();
                $log->debug($line, $rip->job->id());
            }
        }
    }
    
}

?>