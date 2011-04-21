<?php

class RippingCluster_Rips_SourceAudioTrack {
	
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
		return $this->name;
	}
	
	public function setName($name) {
	    $this->name = $name;
	}
	
	public function format() {
		return $this->format;
	}
	
	public function setFormat($format) {
	    $this->format = $format;
	}
	
	public function channels() {
		return $this->channels;
	}
	
	public function setChannels($channels) {
	    $this->channels = $channels;
	}
	
	public function language() {
		return $this->language;
	}
	
	public function setLanguage($language) {
	    $this->language = $language;
	}
	
	public function samplerate() {
		return $this->samplerate;
	}
	
	public function setSampleRate($sample_rate) {
	    $this->samplerate = $sample_rate;
	}
	
	public function bitrate() {
		return $this->bitrate;
	}
	
	public function setBitRate($bit_rate) {
	    $this->bitrate = $bit_rate;
	}
	
};

?>