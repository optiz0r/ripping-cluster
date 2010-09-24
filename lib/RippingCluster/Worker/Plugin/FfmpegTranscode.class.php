<?php

class RippingCluster_Worker_FfmpegTranscode extends RippingCluster_PluginBase implements RippingCluster_Worker_IPlugin {
    
    /**
     * Name of this plugin
     * @var string
     */
    const PLUGIN_NAME = 'FfmpegTranscode';
    
    /**
     * Output produced by the worker process
     * @var string
     */
    private $output;

    /**
     * Gearman Job object describing the task distributed to this worker
     * @var GearmanJob
     */
    private $gearman_job;
    
    /**
     * Ripping Job that is being processed by this Worker
     * @var RippingCluster_Job
     */
    private $job;
    
    /**
     * Associative array of options describing the rip to be carried out
     * @var array(string=>string)
     */
    private $rip_options;
    
    /**
     * Constructs a new instance of this Worker class
     * 
     * @param GearmanJob $gearman_job GearmanJob object describing the task distributed to this worker
     * @throws RippingCluster_Exception_LogicException
     */
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
            'bluray_rip' => array(__CLASS__, 'rip'),
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
        
    /**
     * Executes the process for ripping the source to the final output
     * 
     */
    private function execute() {
        // TODO
    }
    
}

?>