<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerLess extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	private $settings = [
			'less'=>[]
		];

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

		App::import('Vendor', 'Shrink.lessphp', ['file' => 'lessphp'.DS.'lessc.inc.php']);
		$less = new lessc;

		//$less->setFormatter('compressed');
		$less->setPreserveComments(true);
		//$less->setImportDir($file->Folder->path);

		$code = $less->compileFile($file->path);

		return $code;
	}
}
