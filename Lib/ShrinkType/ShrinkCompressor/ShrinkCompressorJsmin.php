<?php

class ShrinkCompressorJsmin extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = array(
			'jsmin'=>array()
		);

	/**
	* Constructer - merges settings with options
	* @return void
	*/
	function __construct($options=array()){
		$this->settings = array_merge_recursive($this->settings, $options);
	}

	/**
	* Compress code
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){

		App::import('Vendor', 'Shrink.jsmin/jsmin');
		$code = trim(JSMin::minify($code));

		return $code;
	}

}