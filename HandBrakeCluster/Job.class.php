<?php

class HandBrakeCluster_Job {

    private $id;
    private $name;
    private $source;
    private $destination;
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
    

    public function __construct($id, $name, $source, $destination, $title, $format, $video_codec, $video_width, $video_height, $quantizer, $deinterlace, $audio_tracks, $audio_codecs, $audio_names, $subtitle_tracks) {
        $this->id              = $id;
        $this->name            = $name;
        $this->source          = $source;
        $this->destination     = $destination;
        $this->title           = $title;
        $this->format          = $format;
        $this->video_codec     = $video_codec;
        $this->video_width     = $video_width;
        $this->video_height    = $video_height;
        $this->quantizer       = $quantizer;
        $this->deinterlace     = $deinterlace;
        $this->audio_tracks    = $audio_tracks;
        $this->audio_codecs    = $audio_codecs;
        $this->audio_names     = $audio_names;
        $this->subtitle_tracks = $subtitle_tracks; 
    }

    public static function fromDatabaseRow($row) {
        return new HandBrakeCluster_Job(
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

    public static function fromId($id) {
        $database = HandBrakeCluster_Main::instance()->database();
        return HandBrakeCluster_Job::fromDatabaseRow(
            $database->selectOne('SELECT * FROM jobs WHERE id=:id', array(
                array('name' => 'id', 'value' => $id, 'type' => PDO::PARAM_INT)
                )
            )
        );
    }

    public static function all() {
        $jobs = array();

        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM jobs') as $row) {
            $jobs[] = self::fromDatabaseRow($row);
        }

        return $jobs;
    }

    public static function allWithStatus($status) {
        $jobs = array();
        
        $database = HandBrakeCluster_Main::instance()->database();
        foreach ($database->selectList('SELECT * FROM jobs WHERE id IN (SELECT id FROM job_status_current WHERE status=:status)', array(
                array('name' => 'status', 'value' => $status, 'type' => PDO::PARAM_INT)
            )) as $row) {
            $jobs[] = self::fromDatabaseRow($row);
        }

        return $jobs;
    }

    protected function loadStatuses() {
        if ($this->statuses == null) {
            $this->statuses = HandBrakeCluster_JobStatus::allForJob($this->id);
        }
    }

    public function currentStatus() {
        $this->loadStatuses();
        return $this->statuses[count($this->statuses) - 1];
    }

    public function id() {
        return $this->id;
    }

    public function name() {
        return $this->name;
    }

    public function source() {
        return $this->source;
    }

    public function destination() {
        return $this->destination;
    }

    public function title() {
        return $this->title;
    }

};

?>
