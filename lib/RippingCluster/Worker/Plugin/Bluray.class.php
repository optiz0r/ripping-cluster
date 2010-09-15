<?php

class RippingCluster_Worker_Bluray extends RippingCluster_PluginBase implements RippingCluster_Worker_IPlugin {
    
    const PLUGIN_NAME = 'Bluray';
    
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
        
    private function execute() {
        // TODO
    }
    
}

?>