<?php

class ShrinkCompilerSass extends ShrinkBase implements ShrinkCompilerInterface{

	public $resultType = 'css';

	private $settings = array(
			'sass'=>array(
					'sass'=>'/usr/bin/sass',
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
		$cmd = $this->settings['sass']['sass'] .' -t '. $style .' '. $file->path;
		$env = array('PATH'=>$this->settings['sass']['path']);
		$code = $this->cmd($cmd, null, $env);

		return $code;
	}
}