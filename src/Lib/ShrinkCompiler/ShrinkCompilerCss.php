<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerCss extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	private $settings = [];

	/**
	* Constructer - merges settings with options
	* @return void
	*/
	function __construct($options=[]){
		$this->settings = array_merge_recursive($this->settings, $options);
	}

	/**
	* Processes/minify/combines queued files of the requested type.
	* @param CakeFile file - 'js' or 'css'. This should be the end result type
	* @return string - code string minified/processed as requested
	*/
	function compile($file){
		return $file->read();
	}
}
