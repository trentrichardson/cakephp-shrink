<?php

class ShrinkCompressorCssmin extends ShrinkBase implements ShrinkCompressorInterface{

	private $settings = array(
			'cssmin'=>array(
				'filters'=>array(),
				'plugins'=>array(
						'CompressColorValues'=>true
					)
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
	* @param string $code - unminified code string
	* @return string - code string minified/processed as requested
	*/
	function compress($code){
		
		App::import('Vendor', 'Shrink.cssmin', array('file' => 'cssmin'.DS.'cssmin.php'));
		$css_minifier = new CssMinifier($code, 
								$this->settings['cssmin']['filters'], 
								$this->settings['cssmin']['plugins'] 
							);
		$code = $css_minifier->getMinified();

		return $code;
	}
}