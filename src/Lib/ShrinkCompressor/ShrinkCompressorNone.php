<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompressorNone extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = [];


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){
		return $code;
	}
}
