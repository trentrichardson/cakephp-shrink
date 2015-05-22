<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompressorJshrink extends ShrinkBase implements ShrinkCompressorInterface{

	protected $settings = [
			'jshrink'=>[
					'flaggedComments'=>false
				]
		];


	/**
	* Determine if there is support for this compressor
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		return class_exists('\JShrink\Minifier');
	}


	/**
	* Compress code
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	public function compress($code){
		
		if($this->isAvailable()){
			$code = \JShrink\Minifier::minify($code, $this->settings['jshrink']);
		}

		return $code;
	}

}
