<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;
use scssc;

class ShrinkCompilerScss extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	protected $settings = [
			'sass'=>[
					'sass'=>'scssphp', // scssphp to use php version, sass to use cmd line version
					'path'=>'/usr/bin'
				]
		];


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param CakeFile file - 'js' or 'css'. This should be the end result type
	* @return string - code string minified/processed as requested
	*/
	function compile($file){
		$code = '';
		$style = 'compact';

		// compile scss
		if($this->settings['sass']['sass'] == 'scssphp'){ // php version
			$scss = new scssc();
			$scss->setImportPaths([$file->Folder->path]);
			$code = $scss->compile($file->read());
		}
		else{ // cmd line version
			$cmd = $this->settings['sass']['sass'] .' -t '. $style .' '. $file->path;
			$env = array('PATH'=>$this->settings['sass']['path']);
			$code = $this->cmd($cmd, null, $env);
		}

		return $code;
	}
}
