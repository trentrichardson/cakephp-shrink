<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;
use lessc;

class ShrinkCompilerLess extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	protected $settings = [
			'less'=>[]
		];


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param CakeFile file - 'js' or 'css'. This should be the end result type
	* @return string - code string minified/processed as requested
	*/
	public function compile($file){

		$less = new lessc;

		//$less->setFormatter('compressed');
		$less->setPreserveComments(true);
		//$less->setImportDir($file->Folder->path);

		$code = $less->compileFile($file->path);

		return $code;
	}
}
