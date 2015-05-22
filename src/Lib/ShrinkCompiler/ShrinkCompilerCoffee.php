<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerCoffee extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'js';

	protected $settings = [
			'coffee'=>[
					'coffee'=>'coffee',
					'path'=>null, // should be something like: '/usr/local/bin/coffee'
					'node'=>null  // should be something like: '/usr/local/bin/node'
				]
		];


	/**
	* Determine if there is support for this compiler
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		$isAvailable = false;

		if($this->settings['coffee']['path'] && $this->settings['coffee']['node']){
			$isAvailable = true;
		}
		else{
			$path = $this->getPath($this->settings['coffee']['coffee']);
			$node = $this->getPath('node');
			if($path !== false && $node !== false){
				$this->settings['coffee']['path'] = $path;
				$this->settings['coffee']['node'] = $node;
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

		// compile coffee script
		if($this->isAvailable()){
			$cmd = $this->settings['coffee']['node'] .' '. $this->settings['coffee']['path'] .' -c -p '. $file->path;
			$code = $this->cmd($cmd, null);
		}
		
		return $code;
	}

}
