<?php
/*
* Shrink CakePHP Plugin to compile, combine, compress css and js.
* Copyright 2015 Trent Richardson < http://trentrichardson.com >
* You may use this project under MIT or GPL licenses.
* http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
* http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
*/
namespace Shrink\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Filesystem\File;
use Shrink\Lib\Shrink;

class ShrinkHelper extends Helper{

	/**
	* @var array - master queue of files
	*/
	private $files = [
			'css'=>[ 'layout'=>[], 'view'=>[] ],
			'js'=>[ 'layout'=>[], 'view'=>[] ]
		];

	/**
	* @var string - what is currently rendering 'view' or 'layout'
	*/
	private $rendering = 'view';

	/**
	* @var Shrink - The instance of Shrink class to do asset mods
	*/
	private $shrink = null;

	/**
	* Constructor - merges options with $this->settings, detects debug level
	* @param View $View - Cake view object currently in use
	* @param array $options - user specified options
	* @return void
	*/
	public function __construct(View $View, $options = []) {
		parent::__construct($View,$options);

		$this->shrink = new Shrink($options);
	}

	/**
	* Event called before the layout is processed, this way we know where to merge the files
	* @param string layoutFile - Name of the file being rendered
	* @return void
	*/
	public function beforeLayout($layoutFile) {
		$this->rendering = 'layout';
	}

	/**
	* Adds a css file to the file queue
	* @param array/string files - string name of a file or array containing multiple string of files
	* @param bool immediate - true to immediately process and print the file, false to merge with others
	* @param string how - 'link' to print <link>, 'embed' to use <style>...css code...</style>
	* @return string - when $immediate=true the tag will be printed, "" otherwise
	*/
	public function css($files, $immediate=false, $how='link') {
		return $this->add('css', $files, $immediate, $how);
	}

	/**
	* Adds a js file to the file queue
	* @param array/string files - string name of a file or array containing multiple string of files
	* @param bool immediate - true to immediately process and print the file, false to merge with others
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @return string - when $immediate=true the tag will be printed, "" otherwise
	*/
	public function js($files, $immediate=false, $how='link') {
		return $this->add('js', $files, $immediate, $how);
	}

	/**
	* Adds a file to the file queue, used internally
	* @param string type - 'js' or 'css'. This should be the end result type
	* @param array files - string name of a file or array containing multiple string of files
	* @param bool immediate - true to immediately process and print the file, false to merge with others
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @return string - when $immediate=true the tag will be printed, "" otherwise
	*/
	protected function add($type, $files, $immediate=false, $how='link') {
		if(!is_array($files)){
			$files = [ $files ];
		}

		// buffering, hold until fetch is called
		if($immediate === false){
			$this->files[$type][$this->rendering] = array_merge($this->files[$type][$this->rendering], $files);
			return '';
		}
		// immediately requested, go ahead and print these out
		else{
			return $this->fetch($type, $how, [ $type=>[ 'layout'=>[], 'view'=>$files]]);
		}
	}

	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string type - 'js' or 'css'. This should be the end result type
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @param array files - string name of a file or array containing multiple string of files
	* @return string - the <script> or <link>
	*/
	function fetch($type, $how='link', $files=[]) {
		if($type == 'script'){
			$type='js';
		}
		if(!$files){
			$files = &$this->files;
		}
		if(!$files){
			return '';
		}

		// ensure the layout files are before the view files
		$files[$type] = array_merge($files[$type]['layout'], $files[$type]['view']);

		// run the shrink utility
		$cacheFile = $this->shrink->build($files[$type], $type);

		// files will be @$this->files, so this clears them
		$files[$type] = [ 'layout'=>[], 'view'=>[] ];

		// print them how the user wants
		if($how == 'embed'){
			$output = $cacheFile['file']->read();
			if($type == 'css'){
				return '<style type="text/css">'. $output .'</style>';
			}
			else{
				return '<script type="text/javascript">'. $output .'</script>';
			}
		}
		else{
			if($type == 'css'){
				return '<link href="'. $cacheFile['webPath'] .'" rel="stylesheet" type="text/css" />';
			}
			else{
				return '<script src="'. $cacheFile['webPath'] .'" type="text/javascript"'. ($how=='async'? ' async ':'') .'></script>';
			}
		}
	}

}
