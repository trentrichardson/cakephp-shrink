<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;
use JSMin;

class ShrinkCompressorJsmin extends ShrinkBase implements ShrinkCompressorInterface{

	protected $settings = [
			'jsmin'=>[]
		];


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
