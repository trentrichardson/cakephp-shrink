<?php

class ShrinkCompressorNone extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = array();

	/**
	* Constructer - merges settings with options
	* @return void
	*/
	function __construct($options=array()){
		$this->settings = array_merge_recursive($this->settings, $options);
	}

	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){
		return $code;
	}
}