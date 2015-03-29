<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;
use JSMin;

class ShrinkCompressorJsmin extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = [
			'jsmin'=>[]
		];

	/**
	* Constructer - merges settings with options
	* @return void
	*/
	function __construct($options=[]){
		$this->settings = array_merge_recursive($this->settings, $options);
	}

	/**
	* Compress code
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){

		$code = trim(JSMin::minify($code));

		return $code;
	}

}
