<?php
namespace Shrink\Lib;



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

        $class = __NAMESPACE__ . '\\ShrinkCompiler\\' .
            'ShrinkCompiler'. strtoupper(substr($type,0,1)).strtolower(substr($type,1));
        return new $class($settings);
	}

	/**
	* Create a new instance of the requested Shrink Compressor class
	* @param string $type - 'jsmin', 'cssmin', etc..
	* @param array $settings - passthrough of complete settings from helper
	* @return class - new instance of the requested type
	*/
	static function getCompressor($type, $settings){

        $class = __NAMESPACE__ . '\\ShrinkCompressor\\' .
            'ShrinkCompressor'. strtoupper(substr($type,0,1)).strtolower(substr($type,1));
        return new $class($settings);
	}
}
