<?php

class HandBrakeCluster_Rips_SourceTitle {
	
	protected $id;
	//protected $vts;
	//protected $ttn;
	//protected $cell_count;
	//protected $blocks;
	
	protected $angles;
	protected $duration;
	protected $width;
	protected $height;
	protected $pixel_aspect;
	protected $display_aspect;
	protected $framerate;
	protected $autocrop;
	
	protected $chapters  = array();
	protected $audio     = array();
	protected $subtitles = array();
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	public function id() {
		return $this->id;
	}
	
	public function angles() {
		return $this->angles;
	}
	
	public function setAngles($angles) {
		$this->angles = $angles;
	}
	
	public function duration() {
		return $this->duration;
	}
	
	public function durationInSeconds() {
	    $time = explode(":", $this->duration);
	    return ($time[0] * 3600) + ($time[1] * 60) + $time[2]; 
	}
	
	public function setDuration($duration) {
		$this->duration = $duration;
	}
	
	public function width() {
		return $this->width;
	}
	
	public function height() {
		return $this->height;
	}
	
	public function displayAspect() {
		return $this->display_aspect;
	}
	
	public function pixelAspect() {
		return $this->pixel_aspect;
	}
	
	public function framerate() {
		return $this->framerate;
	}
	
	public function setDisplayInfo($width, $height, $display_aspect, $pixel_aspect, $framerate) {
		$this->width = $width;
		$this->height = $height;
		$this->pixel_aspect = $pixel_aspect;
		$this->display_aspect = $display_aspect;
		$this->framerate = $framerate;
	}
	
	public function autocrop() {
		return $this->autocrop;
	}
	
	public function setAutocrop($autocrop) {
		$this->autocrop = $autocrop;
	}
	
	public function chapterCount() {
	    return count($this->chapters);
	}
	
	public function chapters() {
		return $this->chapters;
	}
	
	public function addChapter($chapter_id, $duration) {
		$this->chapters[$chapter_id] = $duration;
	}
	
	public function audioTrackCount() {
	    return count($this->audio);
	}
	
	public function audioTracks() {
		return $this->audio;
	}
	
	public function addAudioTrack(HandBrakeCluster_Rips_SourceAudioTrack $audio_track) {
		$this->audio[] = $audio_track;
	}
	
	public function subtitleTrackCount() {
	    return count($this->subtitles);
	}
	
	public function subtitleTracks() {
		return $this->subtitles;
	}
	
	public function addSubtitleTrack(HandBrakeCluster_Rips_SourceSubtitleTrack $subtitle_track) {
		$this->subtitles[] = $subtitle_track;
	}
	
};

?>