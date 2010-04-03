<?php

class HandBrakeCluster_Job {

    protected $source;

    private $id;
    private $name;
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

    private $statuses = null;
    
    private static $cache = array();

    protected function __construct($source, $id, $name, $source_filename, $destination_filename, $title, $format, $video_codec, $video_width, $video_height, $quantizer, $deinterlace, 
            $audio_tracks, $audio_codecs, $audio_names, $subtitle_tracks) {
        $this->source                   = $source;
        $this->id                       = $id;
        $this->name                     = $name;
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

    public static function fromDatabaseRow($row) {
        return new HandBrakeCluster_Job(
            HandBrakeCluster_Rips_Source::load($rips['source']),
            $row['id'],
            $row['name'],
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
     * @return HandBrakeCluster_Job
     */
    public static function fromId($id) {
        $database = HandBrakeCluster_Main::instance()->database();
        
        if (isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        
        $job = HandBrakeCluster_Job::fromDatabaseRow(
            $database->selectOne('SELECT * FROM jobs WHERE id=:id', array(
                array('name' => 'id', 'value' => $id, 'type' => PDO::PARAM_INT)
                )
            )
        );
        
        self::$cache[$job->id] = $job;
    }

    public static function all() {
        $jobs = array();

        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM jobs') as $row) {
            $job = self::fromDatabaseRow($row);
            
            self::$cache[$job->id] = $job;
            $jobs[] = $job;
        }

        return $jobs;
    }

    public static function allWithStatus($status) {
        $jobs = array();
        
        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM jobs WHERE id IN (SELECT job_id FROM job_status_current WHERE status=:status)', array(
                array('name' => 'status', 'value' => $status, 'type' => PDO::PARAM_INT)
            )) as $row) {
            $jobs[] = self::fromDatabaseRow($row);
        }

        return $jobs;
    }
    
    public static function fromPostRequest($source_id, $config) {
        $source_filename = base64_decode(str_replace('-', '/', HandBrakeCluster_Main::issetelse($source_id, HandBrakeCluster_Exception_InvalidParameters)));
        $source 		 = HandBrakeCluster_Rips_Source::load($source_filename);

        $jobs = array();
        foreach ($config as $title => $details) {
            if (HandBrakeCluster_Main::issetelse($details['queue'])) {
                $job = new HandBrakeCluster_Job(
                    $source,
                    null,
                    HandBrakeCluster_Main::issetelse($details['name'], 'unnamed job'),
                    $source->filename(),
                    HandBrakeCluster_Main::issetelse($details['output_filename'], HandBrakeCluster_Exception_InvalidParameters),
                    $title,
                    'mkv',   // @todo Make this configurable
                    'x264',  // @todo Make this configurable 
                    0,    // @todo Make this configurable
                    0,    // @todo Make this configurable
                    0.61,    // @todo Make this configurable
                    HandBrakeCluster_Main::issetelse($details['deinterlace'], 2),
                    implode(',', HandBrakeCluster_Main::issetelse($details['audio'], array())),
                    implode(',', array_pad(array(), count($details['audio']), 'ac3')), // @todo Make this configurable
                    implode(',', array_pad(array(), count($details['audio']), 'Unknown')), // @todo Make this configurable
                    implode(',', HandBrakeCluster_Main::issetelse($details['subtitles'], array()))
                );
                $job->create();

                $jobs[] = $job;
            }
        }

        return $jobs;
    }

    protected function create() {
        $database = HandBrakeCluster_Main::instance()->database();
        $database->insert(
        	'INSERT INTO jobs 
        	(id,name,source,destination,title,format,video_codec,video_width,video_height,quantizer,deinterlace,audio_tracks,audio_codecs,audio_names,subtitle_tracks)
        	VALUES(NULL,:name,:source,:destination,:title,:format,:video_codec,:video_width,:video_height,:quantizer,:deinterlace,:audio_tracks,:audio_codecs,:audio_names,:subtitle_tracks)',
            array(
                array(name => 'name',            value => $this->name,                 type => PDO::PARAM_STR),
                array(name => 'source',          value => $this->source_filename,      type => PDO::PARAM_STR),
                array(name => 'destination',     value => $this->destination_filename, type => PDO::PARAM_STR),
                array(name => 'title',           value => $this->title,                type => PDO::PARAM_INT),
                array(name => 'format',          value => $this->format,               type => PDO::PARAM_STR),
                array(name => 'video_codec',     value => $this->video_codec,          type => PDO::PARAM_STR),
                array(name => 'video_width',     value => $this->video_width,          type => PDO::PARAM_INT),
                array(name => 'video_height',    value => $this->video_height,         type => PDO::PARAM_INT),
                array(name => 'quantizer',       value => $this->quantizer,            type => PDO::PARAM_INT),
                array(name => 'deinterlace',     value => $this->deinterlace,          type => PDO::PARAM_INT),
                array(name => 'audio_tracks',    value => $this->audio_tracks,         type => PDO::PARAM_STR),
                array(name => 'audio_codecs',    value => $this->audio_codecs,         type => PDO::PARAM_STR),
                array(name => 'audio_names',     value => $this->audio_names,          type => PDO::PARAM_STR),
                array(name => 'subtitle_tracks', value => $this->subtitle_tracks,      type => PDO::PARAM_STR),
            )
        );
        
        $this->id = $database->lastInsertId();
        $status = HandBrakeCluster_JobStatus::updateStatusForJob($this, HandBrakeCluster_JobStatus::CREATED);
    }

    public function queue($gearman) {
        $main = HandBrakeCluster_Main::instance();
        $config = $main->config();
        $log = $main->log();
        $log->info('Starting job', $this->id);
        
        // Construct the rip options
        $rip_options = array(
            'nice'            => $config->get('rips.nice', 15),
            'input_dir'       => dirname($this->source_filename) . DIRECTORY_SEPARATOR,
            'input_filename'  => basename($this->source_filename),
            'output_dir'      => dirname($this->destination_filename) . DIRECTORY_SEPARATOR,
            'output_filename' => basename($this->destination_filename),
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
        
        // Enqueue this rip
        $task = $gearman->addTask('handbrake_rip', serialize($rip_options), $config->get('rips.context'), $this->id);
        if ($task) {
            $log->debug("Queued job", $this->id);
            $this->updateStatus(HandBrakeCluster_JobStatus::QUEUED);
        } else {
            $log->warning("Failed to queue job", $this->id);
            $this->updateStatus(HandBrakeCluster_JobStatus::FAILED);
        }
    }

    protected function loadStatuses() {
        if ($this->statuses == null) {
            $this->statuses = HandBrakeCluster_JobStatus::allForJob($this);
        }
    }

    /**
     * 
     * @return HandBrakeCluster_JobStatus
     */
    public function currentStatus() {
        $this->loadStatuses();
        return $this->statuses[count($this->statuses) - 1];
    }
    
    public function updateStatus($new_status, $rip_progress = null) {
        return HandBrakeCluster_JobStatus::updateStatusForJob($this, $new_status, $rip_progress);
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
        HandBrakeCluster_BackgroundTask::run('/usr/bin/php run-jobs.php');
    }
    
};

?>
