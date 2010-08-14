<?php

class HandBrakeCluster_Rips_SourceAudioTrack {
	
	protected $id;
	protected $name;
	protected $format;
	protected $channels;
	protected $language;
	protected $samplerate;
	protected $bitrate;
	
	public function __construct($id, $name, $format, $channels, $language, $samplerate, $bitrate) {
		$this->id = $id;
		$this->name = $name;
		$this->format = $format;
		$this->channels = $channels;
		$this->language = $language;
		$this->samplerate = $samplerate;
		$this->bitrate = $bitrate;
	}
	
	public function id() {
		return $this->id;
	}
	
	public function name() {
		return $name;
	}
	
	public function format() {
		return $this->format;
	}
	
	public function channels() {
		return $this->channels;
	}
	
	public function language() {
		return $this->language;
	}
	
	public function samplerate() {
		return $this->samplerate;
	}
	
	public function bitrate() {
		return $this->bitrate;
	}
	
};

?>