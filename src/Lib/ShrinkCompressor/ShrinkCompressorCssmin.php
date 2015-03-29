<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;
use CssMin;

class ShrinkCompressorCssmin extends ShrinkBase implements ShrinkCompressorInterface{

	protected $settings = [
			'cssmin'=>[]
		];


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	public function compress($code){

        $code = CssMin::minify($code);

		return $code;
	}
}
