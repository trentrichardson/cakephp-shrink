<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;
use CssMin;

class ShrinkCompressorCssmin extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = [
			'cssmin'=>[]
		];


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){

        $code = CssMin::minify($code);

		return $code;
	}
}
