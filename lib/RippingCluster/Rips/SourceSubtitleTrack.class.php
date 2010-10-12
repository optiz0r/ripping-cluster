<?php

class RippingCluster_Rips_SourceSubtitleTrack {
	
	protected $id;
	protected $name;
	protected $language;
	protected $format;
	
	public function __construct($id, $name, $language, $format) {
		$this->id = $id;
		$this->name = $name;
		$this->language = $language;
		$this->format = $format;
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
	
	public function language() {
		return $this->language;
	}
	
	public function setLanguage($language) {
	    $this->language = $language;
	}
	
	public function format() {
		return $this->format;
	}
	
	public function setFormat($format) {
	    $this->format = $format;
	}
	
};

?>