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
					'path'=>null       // should be something like: '/usr/bin/sass'
				]
		];


	/**
	* Determine if there is support for this compiler
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		$isAvailable = false;

		if($this->settings['sass']['sass'] == 'scssphp' && class_exists('scssc')){
			$isAvailable = true;
		}
		else if($this->settings['sass']['path']){
			$isAvailable = true;
		}
		else{
			$path = $this->getPath($this->settings['sass']['sass']);
			if($path !== false){
				$this->settings['sass']['path'] = $path;
				$isAvailable = true;
			}
		}
		return $isAvailable;
	}


	/**
	* Processes/minify/combines queued files of the requested type.
	* @param CakeFile file - 'js' or 'css'. This should be the end result type
	* @return string - code string minified/processed as requested
	*/
	public function compile($file){
		$code = '';
		$style = 'compact';

		if($this->isAvailable()){
			// compile scss
			if($this->settings['sass']['sass'] == 'scssphp'){ // php version
				$scss = new scssc();
				$scss->setImportPaths([$file->Folder->path]);
				$code = $scss->compile($file->read());
			}
			else{ // cmd line version
				$cmd = $this->settings['sass']['path'] .' -t '. $style .' '. $file->path;
				$env = [ 'PATH'=>pathinfo($this->settings['sass']['path'], PATHINFO_DIRNAME) ];
				$code = $this->cmd($cmd, null, $env);
				var_dump([$cmd,$env,$this->_error]);
				// $cmd = $this->settings['sass']['path'] .' -t '. $style .' '. $file->path;
				// $code = $this->cmd($cmd, null);
			}
		}

		return $code;
	}
}
