<?php
namespace Shrink\Lib\ShrinkCompiler;

use Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface;
use Shrink\Lib\ShrinkBase;

class ShrinkCompilerTs extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'js';

	protected $settings = [
			'typescript'=>[
					'typescript'=>'tsc',
					'path'=>null, // should be something like: '/usr/local/bin/typescript'
					'node'=>null,  // should be something like: '/usr/local/bin/node'
					'node_modules'=>null // should be something like '/usr/local/lib/node_modules'
				]
		];


	/**
	* Determine if there is support for this compiler
	* @return boolean - true if there is support
	*/
	public function isAvailable(){
		$isAvailable = false;

		if($this->settings['typescript']['path'] && $this->settings['typescript']['node']){
			$isAvailable = true;
		}
		else{
			$path = $this->getPath($this->settings['typescript']['typescript']);
			$node = $this->getPath('node');
			if($path !== false && $node !== false){
				$this->settings['typescript']['path'] = $path;
				$this->settings['typescript']['node'] = $node;
				$this->settings['typescript']['node_modules'] = 
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

		// compile typescript
		if($this->isAvailable()){
			$tmpFile = tempnam(TMP, 'TYPESCRIPT');
			$cmd = $this->settings['typescript']['node'] .' '. $this->settings['typescript']['path'] . ' ' . $file->path . ' --out ' . $tmpFile;
			$env = [ 'NODE_PATH'=>$this->settings['typescript']['node_modules'] ];
			$this->cmd($cmd, null, $env);
			$code = file_get_contents($tmpFile);
			unlink($tmpFile);
		}
		
		return $code;
	}

}
