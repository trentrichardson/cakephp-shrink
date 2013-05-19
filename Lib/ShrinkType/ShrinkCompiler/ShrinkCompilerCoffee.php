<?php

class ShrinkCompilerCoffee extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'js';

	private $settings = array(
			'coffee'=>array(
					'coffee'=>'/usr/local/bin/coffee',
					'node' => '/usr/local/bin/node',
					'node_path' => '/usr/local/lib/node_modules'
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

		// compile coffee script
		$cmd = $this->settings['coffee']['node'] .' '. $this->settings['coffee']['coffee'] .' -c -p '. $file->path;
		$env = array('NODE_PATH'=>$this->settings['coffee']['node_path']);
		$code = $this->run($cmd, null, $env);

		return $code;
	}

}