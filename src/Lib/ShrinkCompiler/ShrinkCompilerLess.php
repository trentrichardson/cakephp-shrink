<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;
use lessc;

class ShrinkCompilerLess extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	protected $settings = [
			'less'=>[
				'less' => 'lessphp', // lessphp to use the php version (default) or lessc
				'path' => null,      // should be something like: '/usr/local/bin/lessc'
				'node'=>null,        // should be something like: '/usr/local/bin/node'
				'node_modules'=>null // should be something like '/usr/local/lib/node_modules'
			]
		];


	/**
	* Determine if there is support for this compiler
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		$isAvailable = false;

		if($this->settings['less']['less'] == 'lessphp' && class_exists('lessc')){
			$isAvailable = true;
		}
		else if($this->settings['less']['path']){
			$isAvailable = true;
		}
		else{
			$path = $this->getPath($this->settings['less']['less']);
			$node = $this->getPath('node');
			if($path !== false && $node !== false){
				$this->settings['less']['path'] = $path;
				$this->settings['less']['node'] = $node;
				$this->settings['less']['node_modules'] = 
					str_replace('bin'.DS.'node', 'lib'.DS.'node_modules', $node);
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

		if($this->isAvailable()){
			if($this->settings['less']['less'] === 'lessphp'){
				$less = new lessc;
				$less->setPreserveComments(true);
				//$less->setImportDir($file->Folder->path);
				$code = $less->compileFile($file->path);
			}
			else{
				$cmd = $this->settings['less']['node'] .' '. $this->settings['less']['path'] .' '. $file->path;
				$env = [ 'NODE_PATH'=>$this->settings['less']['node_modules'] ];
				$code = $this->cmd($cmd, null, $env);
			}
		}
		
		return $code;
	}
}
