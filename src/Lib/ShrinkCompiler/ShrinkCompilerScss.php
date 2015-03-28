<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerScss extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	private $settings = array(
			'sass'=>array(
					'sass'=>'scssphp', // scssphp to use php version, sass to use cmd line version
					'path'=>'/usr/bin'
				)
		);

	/**
	* Constructer - merges settings with options
	* @return void
	*/
	function __construct($options=array()){
		$this->settings = array_merge_recursive($this->settings, $options);
	}

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
			App::import('Vendor', 'Shrink.scssphp', array('file' => 'scssphp'.DS.'scss.inc.php'));
			$scss = new scssc();
			$scss->setImportPaths(array($file->Folder->path));
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
