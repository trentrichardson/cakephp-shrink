<?php
namespace Shrink\Lib\ShrinkCompressor;

use Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompressorJShrink extends ShrinkBase implements ShrinkCompressorInterface{

	protected $settings = [
			'jshrink'=>[
					'flaggedComments'=>false
				]
		];


	/**
	* Compress code
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	public function compress($code){

		$code = \JShrink\Minifier::minify($code, [
					'flaggedComments' => $this->settings['jshrink']['flaggedComments']
				]);

		return $code;
	}

}
