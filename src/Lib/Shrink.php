<?php
namespace Shrink\Lib;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Shrink\Lib\ShrinkType;


/**
* Class to coordinate Compile, Compress, Combine
*/
class Shrink{

	/**
	* @var array settings - overall settings
	*/
	public $settings = [
		'js'=>[
				'path'=>'js/',        // folder to find src js files
				'cachePath'=>'js/',   // folder to create cache files
				'minifier'=>'jshrink' // minifier to minify, false to leave as is
			],
		'css'=>[
				'path'=>'css/',       // folder to find src css files
				'cachePath'=>'css/',  // folder to create cache files
				'minifier'=>'cssmin', // minifier name to minify, false to leave as is
				'charset'=>'utf-8'    // charset to use
			],
		'url'=>'',                    // url without ending /, incase you access from another domain
		'prefix'=>'shrink_',          // prefix the beginning of cache files
		'debugLevel'=>1               // compared against Core.debug, eq will recompile, > will not minify
	];

	/**
	* @var array - stores the instances of the compilers
	*/
	private $compilers = [];

	/**
	* @var array - stores the instances of the compressors
	*/
	private $compressors = [];

	/**
	* @var string - if no url rewrites this is the extra path
	*/
	private $extraPath = '';

	/**
	* @var boolean - if debugging level is reached
	*/
	public $debugging = false;


	/**
	* Constructor - merges settings with options
	* @param array $options - options to set for compilers and compressors
	* @return void
	*/
	function __construct($options=[]) {
		$this->settings = array_replace_recursive($this->settings, (array) Configure::read('Shrink'), $options);

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
	* build - Compile, Compress, Combile the array of files
	* @param array $files - filenames to process
	* @param string $type - js or css to indicate how to compress and which options to use
	* @param string $cacheFile - filename to write the results to, relative to cachePath option
	* @return array - array with the cache file object and the new web path ['file','webPath']
	*/
	function build($files, $type, $cacheFile='') {

		// determine the cache file path
		if($cacheFile === ''){
			$cacheFile = $this->settings['prefix'] . md5(implode('_', $files)) .'.'. $type;
		}
		$cacheFilePath = preg_replace('/(\/+|\\+)/', DS, WWW_ROOT .DS. $this->settings[$type]['cachePath'] .DS. $cacheFile);
		$cacheFileObj = new File($cacheFilePath);
		$webCacheFilePath = $this->settings['url'] . preg_replace('/\/+/', '/', '/'. $this->extraPath .'/'. $this->settings[$type]['cachePath'] . $cacheFile);

		// create Cake file objects and get the max date
		$maxAge = 0;
		foreach($files as $k=>$v){
			$tmpf = new File(preg_replace('/(\/+|\\+)/', DS, WWW_ROOT .DS. ($v[0]=='/'? '':$this->settings[$type]['path']) .DS. $v));
			$files[$k] = [
					'file'=>$tmpf,
					'rel_path'=>$v
				];
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

			foreach($files as $k=>$v){
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

				// we are done with this file, close it
				$v['file']->close();
			}

			// be sure no duplicate charsets
			if($type == 'css'){
				$output = preg_replace('/@charset\s+[\'"].+?[\'"];?/i','',$output);
				$output = '@charset "'. $this->settings['css']['charset'] ."\";\n". $output;
			}

			// write the file
			$cacheFileObj->write($output);
		}

		$ret = [
			'path'=>$cacheFileObj->path,
			'webPath'=>$webCacheFilePath
		];
		$cacheFileObj->close();

		return $ret;
	}
}