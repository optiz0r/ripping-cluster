<?php

class RippingCluster_Job {

    protected $source;

    private $id;
    private $name;
    private $source_plugin;
    private $rip_plugin;
    private $source_filename;
    private $destination_filename;
    private $title;
    private $format;
    private $video_codec;
    private $video_width;
    private $video_height;
    private $quantizer;
    private $deinterlace;
    private $audio_tracks;
    private $audio_codecs;
    private $audio_names;
    private $subtitle_tracks; 

    /**
     * 
     * @var array(RippingCluster_JobStatus)
     */
    private $statuses = null;
    
    private static $cache = array();

    protected function __construct($source, $id, $name, $source_plugin, $rip_plugin, $source_filename, $destination_filename, $title, $format, $video_codec, $video_width, $video_height, $quantizer, $deinterlace, 
            $audio_tracks, $audio_codecs, $audio_names, $subtitle_tracks) {
        $this->source                   = $source;
        $this->id                       = $id;
        $this->name                     = $name;
        $this->source_plugin            = $source_plugin;
        $this->rip_plugin               = $rip_plugin;
        $this->source_filename          = $source_filename;
        $this->destination_filename     = $destination_filename;
        $this->title                    = $title;
        $this->format                   = $format;
        $this->video_codec              = $video_codec;
        $this->video_width              = $video_width;
        $this->video_height             = $video_height;
        $this->quantizer                = $quantizer;
        $this->deinterlace              = $deinterlace;
        $this->audio_tracks             = $audio_tracks;
        $this->audio_codecs             = $audio_codecs;
        $this->audio_names              = $audio_names;
        $this->subtitle_tracks          = $subtitle_tracks; 
    }

    public function __clone() {
        $this->id = null;

        $this->create();
    }

    public static function fromDatabaseRow($row) {
        return new RippingCluster_Job(
            RippingCluster_Source_PluginFactory::load($row['source_plugin'], $row['source'], false),
            $row['id'],
            $row['name'],
            $row['source_plugin'],
            $row['rip_plugin'],
            $row['source'],
            $row['destination'],
            $row['title'],
            $row['format'],
            $row['video_codec'],
            $row['video_width'],
            $row['video_height'],
            $row['quantizer'],
            $row['deinterlace'],
            $row['audio_tracks'],
            $row['audio_codecs'],
            $row['audio_names'],
            $row['subtitle_tracks']
        );
    }

    /**
     * 
     * @todo Implement cache of previously loaded jobs
     * 
     * @param int $id
     * @return RippingCluster_Job
     */
    public static function fromId($id) {
        $database = RippingCluster_Main::instance()->database();
        
        if (isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        
        $job = RippingCluster_Job::fromDatabaseRow(
            $database->selectOne('SELECT * FROM jobs WHERE id=:id', array(
                    array('name' => 'id', 'value' => $id, 'type' => PDO::PARAM_INT)
                )
            )
        );
        
        self::$cache[$job->id] = $job;

        return $job;
    }

    public static function all() {
        $jobs = array();

        $database = RippingCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM jobs WHERE id > 0') as $row) {
            $job = self::fromDatabaseRow($row);
            
            self::$cache[$job->id] = $job;
            $jobs[] = $job;
        }

        return $jobs;
    }

    public static function allWithStatus($status, $limit = null) {
        $jobs = array();
        $database = RippingCluster_Main::instance()->database();
        
        $params = array(
            array('name' => 'status', 'value' => $status, 'type' => PDO::PARAM_INT),        
        );
        
        $limitSql = '';
        if ($limit) {
            $limitSql = 'LIMIT :limit';
            $params[] = array('name' => 'limit',  'value' => $limit,  'type' => PDO::PARAM_INT);
        }
        
        foreach ($database->selectList("SELECT * FROM jobs WHERE id IN (SELECT job_id FROM job_status_current WHERE id > 0 AND status=:status) ORDER BY id DESC {$limitSql}", $params) as $row) {
            $jobs[] = self::fromDatabaseRow($row);
        }

        return $jobs;
    }
    
    public static function fromPostRequest($plugin, $source_id, $global_options, $titles) {
        $source = RippingCluster_Source_PluginFactory::loadEncoded($plugin, RippingCluster_Main::issetelse($source_id, 'RippingCluster_Exception_InvalidParameters'));

        $jobs = array();
        foreach ($titles as $title => $details) {
            if (RippingCluster_Main::issetelse($details['queue'])) {
                RippingCluster_Main::issetelse($details['output_filename'], 'RippingCluster_Exception_InvalidParameters');
                
                $job = new RippingCluster_Job(
                    $source,
                    null,
                    RippingCluster_Main::issetelse($details['name'], 'unnamed job'),
                    $source->plugin(),
                    $plugin,
                    $source->filename(),
                    $global_options['output-directory'] . DIRECTORY_SEPARATOR . $details['output_filename'],
                    $title,
                    $global_options['format'],
                    $global_options['video-codec'], 
                    $global_options['video-width'],
                    $global_options['video-height'],
                    $global_options['quantizer'],
                    RippingCluster_Main::issetelse($details['deinterlace'], 2),
                    implode(',', RippingCluster_Main::issetelse($details['audio'], array())),
                    implode(',', array_pad(array(), count($details['audio']), 'ac3')), // @todo Make this configurable
                    implode(',', array_pad(array(), count($details['audio']), 'Unknown')), // @todo Make this configurable
                    implode(',', RippingCluster_Main::issetelse($details['subtitles'], array()))
                );
                $job->create();

                $jobs[] = $job;
            }
        }
        
        return $jobs;
    }

    protected function create() {
        $database = RippingCluster_Main::instance()->database();
        $database->insert(
        	'INSERT INTO jobs 
        	(id,name,source,destination,title,format,video_codec,video_width,video_height,quantizer,deinterlace,audio_tracks,audio_codecs,audio_names,subtitle_tracks)
        	VALUES(NULL,:name,:source,:destination,:title,:format,:video_codec,:video_width,:video_height,:quantizer,:deinterlace,:audio_tracks,:audio_codecs,:audio_names,:subtitle_tracks)',
            array(
                array('name' => 'name',            'value' => $this->name,                 'type' => PDO::PARAM_STR),
                array('name' => 'source',          'value' => $this->source_filename,      'type' => PDO::PARAM_STR),
                array('name' => 'destination',     'value' => $this->destination_filename, 'type' => PDO::PARAM_STR),
                array('name' => 'title',           'value' => $this->title,                'type' => PDO::PARAM_INT),
                array('name' => 'format',          'value' => $this->format,               'type' => PDO::PARAM_STR),
                array('name' => 'video_codec',     'value' => $this->video_codec,          'type' => PDO::PARAM_STR),
                array('name' => 'video_width',     'value' => $this->video_width,          'type' => PDO::PARAM_INT),
                array('name' => 'video_height',    'value' => $this->video_height,         'type' => PDO::PARAM_INT),
                array('name' => 'quantizer',       'value' => $this->quantizer,            'type' => PDO::PARAM_INT),
                array('name' => 'deinterlace',     'value' => $this->deinterlace,          'type' => PDO::PARAM_INT),
                array('name' => 'audio_tracks',    'value' => $this->audio_tracks,         'type' => PDO::PARAM_STR),
                array('name' => 'audio_codecs',    'value' => $this->audio_codecs,         'type' => PDO::PARAM_STR),
                array('name' => 'audio_names',     'value' => $this->audio_names,          'type' => PDO::PARAM_STR),
                array('name' => 'subtitle_tracks', 'value' => $this->subtitle_tracks,      'type' => PDO::PARAM_STR),
            )
        );
        
        $this->id = $database->lastInsertId();
        $status = RippingCluster_JobStatus::updateStatusForJob($this, RippingCluster_JobStatus::CREATED);
    }

    public function delete() {
        $database = RippingCluster_Main::instance()->database();
        $database->update(
            'DELETE FROM jobs WHERE id=:job_id LIMIT 1',
            array(
                array(name => 'job_id', value => $this->id, type => PDO::PARAM_INT),
            )    
        );

        $this->id = null;
    }

    public function queue() {
        $main = RippingCluster_Main::instance();
        $config = $main->config();
        
        // Construct the rip options
        $rip_options = array(
            'id'              => $this->id,
            'nice'            => $config->get('rips.nice', 15),
            'input_filename'  => $this->source_filename,
            'output_filename' => $this->destination_filename,
            'title'           => $this->title,
            'format'          => $this->format,
            'video_codec'     => $this->video_codec,
            'video_width'     => $this->video_width,
            'video_height'    => $this->video_height,
            'quantizer'       => $this->quantizer,
            'deinterlace'     => $this->deinterlace,
            'audio_tracks'    => $this->audio_tracks,
            'audio_codec'     => $this->audio_codecs,
            'audio_names'     => $this->audio_names,
            'subtitle_tracks' => $this->subtitle_tracks,
        );
        
        return array('HandBrake', array('rip_options' => $rip_options));
    }

    protected function loadStatuses() {
        if ($this->statuses == null) {
            $this->statuses = RippingCluster_JobStatus::allForJob($this);
        }
    }

    /**
     * 
     * @return RippingCluster_JobStatus
     */
    public function currentStatus() {
        $this->loadStatuses();
        return $this->statuses[count($this->statuses) - 1];
    }
    
    public function updateStatus($new_status, $rip_progress = null) {
        $this->loadStatuses();
        
        // Only update the status if the state is changing
        if ($this->currentStatus()->status() != $new_status) {
            $new_status = RippingCluster_JobStatus::updateStatusForJob($this, $new_status, $rip_progress);
            $this->statuses[] = $new_status;
        }
        
        return $new_status;
    }

    public function calculateETA() {
        $current_status = $this->currentStatus();
        if ($current_status->status() != RippingCluster_JobStatus::RUNNING) {
            throw new RippingCluster_Exception_JobNotRunning();
        }

        $running_time = $current_status->mtime() - $current_status->ctime();
        $progress     = $current_status->ripProgress();
        
        $remaining_time = 0;
        if ($progress > 0) {
            $remaining_time = round((100 - $progress) * ($running_time / $progress));
        }

        return $remaining_time;
    }
    
    public function fixBrokenTimestamps() {
        $this->loadStatuses();
        
        // See if we have both a RUNNING and a COMPLETE status set
        $statuses = array();
        foreach ($this->statuses as $status) {
            switch ($status->status()) {
                case RippingCluster_JobStatus::RUNNING:
                case RippingCluster_JobStatus::COMPLETE:
                    $statuses[$status->status()] = $status;
                    break;
            }
        }
        
        if (isset($statuses[RippingCluster_JobStatus::RUNNING]) && isset($statuses[RippingCluster_JobStatus::COMPLETE])) {
            // Ensure the timestamp on the complete is >= that of the running status
            if ($statuses[RippingCluster_JobStatus::COMPLETE]->mtime() < $statuses[RippingCluster_JobStatus::RUNNING]->mtime()) {
                $statuses[RippingCluster_JobStatus::COMPLETE]->mtime($statuses[RippingCluster_JobStatus::RUNNING]->mtime() + 1);
                $statuses[RippingCluster_JobStatus::COMPLETE]->save();
            }
        }        
    }
    
    public function id() {
        return $this->id;
    }

    public function name() {
        return $this->name;
    }

    public function sourceFilename() {
        return $this->source_filename;
    }

    public function destinationFilename() {
        return $this->destination_filename;
    }

    public function title() {
        return $this->title;
    }

    public static function runAllJobs() {
        RippingCluster_BackgroundTask::run('/usr/bin/php ' . RippingCluster_Main::makeAbsolutePath('run-jobs.php'));
    }
    
};

?>
