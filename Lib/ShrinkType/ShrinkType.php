<?php

/**
* Interface for each Shrink Compiler abstraction
*/
interface ShrinkCompilerInterface{

	public function compile($file);

}

/**
* Interface for each Shrink Compressor abstraction
*/
interface ShrinkCompressorInterface{

	public function compress($file);

}

/**
* base class with some helper functions
*/
class ShrinkBase{
	protected $_error;

	/**
	* Run a command and return the output
	* @param string $cmd command to run
	* @param string $input input to pass to the command STDIN
	* @param array $env - key value pairs of environment variables to be set
	* @return string - output from the command
	*/
	public function cmd($cmd, $input=null, $env=array()){
		$out = '';
		$descriptorSpec = array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w'));

		$process = proc_open($cmd, $descriptorSpec, $pipes, null, $env);

		if (is_resource($process)) {
			fwrite($pipes[0], $input);
			fclose($pipes[0]);

			$out = stream_get_contents($pipes[1]);
			fclose($pipes[1]);

			$this->run_error = stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			proc_close($process);
		}
		return $out;
	}
}


/**
* External utility class to retrieve an instance
*/
class ShrinkType{

	/**
	* Create a new instance of the requested Shrink Compiler class
	* @param string $type - 'js', 'css', 'less'. This should be the input type
	* @param array $settings - passthrough of complete settings from helper
	* @return class - new instance of the requested type
	*/
	static function getCompiler($type, $settings){
		$classname = 'ShrinkCompiler'. strtoupper(substr($type,0,1)).strtolower(substr($type,1));
		$filename = 'ShrinkCompiler' .DS. $classname .'.php';
		if(include_once($filename)){
			return new $classname($settings);
		}
	}

	/**
	* Create a new instance of the requested Shrink Compressor class
	* @param string $type - 'jsmin', 'cssmin', etc..
	* @param array $settings - passthrough of complete settings from helper
	* @return class - new instance of the requested type
	*/
	static function getCompressor($type, $settings){
		$classname = 'ShrinkCompressor'. strtoupper(substr($type,0,1)).strtolower(substr($type,1));
		$filename = 'ShrinkCompressor' .DS. $classname .'.php';
		if(include_once($filename)){
			return new $classname($settings);
		}
	}
}