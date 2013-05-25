<?php
/*
* Shrink CakePHP Plugin to compile, combine, compress css and js.
* Copyright 2013 Trent Richardson < http://trentrichardson.com >
* You may use this project under MIT or GPL licenses.
* http://trentrichardson.com/Impromptu/GPL-LICENSE.txt
* http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
*/
App::uses('AppHelper','View/Helper');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('ShrinkType','Shrink.Lib/ShrinkType');

class ShrinkHelper extends AppHelper{

	/**
	* @var array - master queue of files
	*/
	private $files = array(
			'css'=>array('layout'=>array(), 'view'=>array()),
			'js'=>array('layout'=>array(), 'view'=>array())
		);

	/**
	* @var string - what is currently rendering 'view' or 'layout'
	*/
	private $rendering = 'view';

	/**
	* @var array - stores the instances of the compilers
	*/
	private $compilers = array();

	/**
	* @var array - stores the instances of the compressors
	*/
	private $compressors = array();
	
	/**
	* @var string - if no url rewrites this is the extra path
	*/
	private $extraPath = '';
	
	/**
	* @var boolean - if debugging level is reached
	*/
	public $debugging = false;

	/**
	* @var array - active settings for this instance.  Merged with passed options
	*/
	public $settings = array(
			'js'=>array(
					'path'=>'js/',        // folder to find src js files
					'cachePath'=>'js/',   // folder to create cache files
					'minifier'=>'jsmin'   // minifier to minify, false to leave as is
				),
			'css'=>array(
					'path'=>'css/',       // folder to find src css files
					'cachePath'=>'css/',  // folder to create cache files
					'minifier'=>'cssmin', // minifier name to minify, false to leave as is
					'charset'=>'utf-8'    // charset to use
				),
			'url'=>'',                     // url without ending /, incase you access from another domain
			'prefix'=>'shrink_',           // prefix the beginning of cache files
			'debugLevel'=>1                // compared against Core.debug, eq will recompile, > will not minify
		);

	/**
	* Constructor - merges options with $this->settings, detects debug level
	* @param View $View - Cake view object currently in use
	* @param array $options - user specified options
	* @return void
	*/
	public function __construct(View $View, $options = array()) {
		parent::__construct($View,$options);
		
		// URL Rewrites are switched off, so wee need to add this extra path.
		if(Configure::read('App.baseUrl')){
			$this->extraPath = 'app/webroot/';
		}

		// Determine the debug level to see if we should minify
		$cakedebug = Configure::read('debug');
		if($cakedebug >= $this->settings['debugLevel']){
			$this->debugging = true;
			if($cakedebug == 2){
				$this->settings['js']['minifier'] = false;
				$this->settings['minify']['minifier'] = false;
			}
		}

		if($this->settings['js']['minifier'] === false)
			$this->settings['js']['minifier'] = 'none';
		if($this->settings['css']['minifier'] === false)
			$this->settings['css']['minifier'] = 'none';
	}

	/**
	* Event called before the layout is processed, this way we know where to merge the files
	* @param string layoutFile - Name of the file being rendered
	* @return void
	*/
	public function beforeLayout($layoutFile){
		$this->rendering = 'layout';
	}

	/**
	* Adds a css file to the file queue
	* @param array/string files - string name of a file or array containing multiple string of files
	* @param bool buffer - false to immediately process and print the file, true to merge with others
	* @param string how - 'link' to print <link>, 'embed' to use <style>...css code...</style>
	* @return string - when $buffer=false the tag will be printed, "" otherwise
	*/
	public function css($files, $buffer=false, $how='link'){
		return $this->add('css', $files, $buffer, $how);
	}

	/**
	* Adds a js file to the file queue
	* @param array/string files - string name of a file or array containing multiple string of files
	* @param bool buffer - false to immediately process and print the file, true to merge with others
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @return string - when $buffer=false the tag will be printed, "" otherwise
	*/
	public function js($files, $buffer=false, $how='link'){
		return $this->add('js', $files, $buffer, $how);
	}
	
	/**
	* Adds a file to the file queue, used internally
	* @param string type - 'js' or 'css'. This should be the end result type
	* @param array files - string name of a file or array containing multiple string of files
	* @param bool buffer - false to immediately process and print the file, true to merge with others
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @return string - when $buffer=false the tag will be printed, "" otherwise
	*/
	protected function add($type, $files, $buffer=false, $how='link') {
		if(!is_array($files)){
			$files = array($files);
		}

		// not buffer, hold until fetch is called
		if($buffer === true){
			$this->files[$type][$this->rendering] = array_merge($this->files[$type][$this->rendering], $files);
			return '';
		}
		// buffer, go ahead and print these out
		else{
			return $this->fetch($type, $how, array($type=>array('layout'=>array(), 'view'=>$files)));
		}
	}

	/**
	* Processes/minify/combines queued files of the requested type.
	* @param string type - 'js' or 'css'. This should be the end result type
	* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
	* @param array files - string name of a file or array containing multiple string of files
	* @return string - the <script> or <link>
	*/
	function fetch($type, $how='link', $files=array()) {
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


		// determine the cache file path
		$cacheFile = $this->settings['prefix'] . md5(implode('_', $files[$type])) .'.'. $type;
		$cacheFilePath = preg_replace('/(\/+|\\+)/', DS, WWW_ROOT .DS. $this->settings[$type]['cachePath'] .DS. $cacheFile);
		$cacheFileObj = new File($cacheFilePath);
		$webCacheFilePath = $this->settings['url'] . preg_replace('/\/+/', '/', '/'. $this->extraPath .'/'. $this->settings[$type]['cachePath'] . $cacheFile);

		// create Cake file objects and get the max date
		$maxAge = 0;
		foreach($files[$type] as $k=>$v){
			$tmpf = new File(preg_replace('/(\/+|\\+)/', DS, WWW_ROOT .DS. ($v[0]=='/'? '':$this->settings[$type]['path']) .DS. $v));
			$files[$type][$k] = array(
					'file'=>$tmpf,
					'rel_path'=>$v
				);
			$srcMod = $tmpf->lastChange();
			if($srcMod > $maxAge){
				$maxAge = $srcMod;
			}
		}

		// has the cache expired (we're debugging, the cache doesn't exist, or too old)?
		$expired = false;
		if($this->debugging || !$cacheFileObj->exists() || $maxAge > $cacheFileObj->lastChange()){
			$expired = true;
		}

		// rebuild if it has expired
		if($expired){

			$output = '';

			foreach($files[$type] as $k=>$v){
				$lang = $v['file']->ext();

				// load compiler if it is not already
				if(!isset($this->compilers[$lang])){
					$this->compilers[$lang] = ShrinkType::getCompiler($lang,$this->settings);
				}
				$resultType = $this->compilers[$lang]->resultType;

				// load the compressor if it is not already
				$compressorName = $this->settings[$type]['minifier'];
				if(!isset($this->compressors[$compressorName])){
					$this->compressors[$compressorName] = ShrinkType::getCompressor($compressorName,$this->settings);
				}

				// compile, compress, combine
				if($resultType == $type && $v['file']->exists()){
					$output .= "/* ". $v['rel_path'] ." */\n";
					$code = $this->compilers[$lang]->compile($v['file']);
					$code = $this->compressors[$compressorName]->compress($code);
					$output .= $code ."\n";
				}
				
			}

			// be sure no duplicate charsets
			if($type == 'css'){
				$output = preg_replace('/@charset\s+[\'"].+?[\'"];?/i','',$output);
				$output = '@charset "'. $this->settings['css']['charset'] ."\";\n". $output;
			}


			// write the file
			$cacheFileObj->write($output);
		}
		// files will be @$this->files, so this clears them
		$files[$type] = array('layout'=>array(), 'view'=>array());

		// print them how the user wants
		if($how == 'embed'){
			$output = $cacheFileObj->read();
			if($type == 'css'){
				return '<style type="text/css">'. $output .'</style>';
			}
			else{
				return '<script type="text/javascript">'. $output .'</script>';
			}
		}
		else{
			if($type == 'css'){
				return '<link href="'. $webCacheFilePath .'" rel="stylesheet" type="text/css" />';
			}
			else{
				return '<script src="'. $webCacheFilePath .'" type="text/javascript"'. ($how=='async'? ' async ':'') .'></script>';
			}
		}
	}

}