<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerSass extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	protected $settings = [
			'sass'=>[
					'sass'=>'sass',
					'path'=>null       // should be something like: '/usr/bin/sass'
				]
		];


	/**
	* Determine if there is support for this compiler
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		$isAvailable = false;

		if($this->settings['sass']['path']){
			$isAvailable = true;
		}
		else{
			$path = $this->getPath($this->settings['sass']['sass']);
			if($path !== false){
				$this->settings['sass']['path'] = $path;
				$isAvailable = true;
				var_dump($this->settings['sass']['path']);
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

		// compile scss
		if($this->isAvailable()){
			$cmd = $this->settings['sass']['path'] .' -t '. $style .' '. $file->path;
			$code = $this->cmd($cmd, null);
		}

		return $code;
	}
}
