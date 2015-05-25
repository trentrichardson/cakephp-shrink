Shrink CakePHP Plugin
=====================

About
-----

- Author: [Trent Richardson](http://trentrichardson.com)
- Twitter: [@practicalweb](http://twitter.com/practicalweb)

The Shrink plugin compiles, combines, and minifies javascript and css.  It currently has
support for native javascript and css, Less (php version), Sass, and CoffeeScript.

Shrink is a minimal configuration plugin.  For a super powerful, configurable asset minifier
look into [Mark Story's asset_compress](https://github.com/markstory/asset_compress) instead.

Installation
------------

This version is for CakePHP 3, for other versions of CakePHP check the branches on Github.

It is recommended to use composer to install: `"trentrichardson/cakephp-shrink": "~3.0"`.
Then enable your plugin in bootstrap.php `Plugin::load('Shrink');` or `Plugin::loadAll();`.

You can also manually download Shrink and place into app/Plugin folder

Enable the plugin in bootstrap.php with `Plugin::load('Shrink',['autoload'=>true]);` or 
`Plugin::loadAll();`.  When manually installing you need to set the autoload flag to true.

Add "Shrink.Shrink" to your `$helpers` property in your controller.  Likely AppController.php.

Usage
-----

You use Shrink in the same way you already use the Html helper for js and css. The slight
differences are that you must include the file extension (since you can now process less,
sass, coffee), and the parameters following the files.

```php
/**
* Adds a css file to the file queue
* @param array/string files - string name of a file or array containing multiple string of files
* @param bool immediate - true to immediately process and print the file, false to merge with others
* @param string how - 'link' to print <link>, 'embed' to use <style>...css code...</style>
* @return string - when $immediate=true the tag will be printed, "" otherwise
*/
public function css($files, $immediate=false, $how='link')

/**
* Adds a js file to the file queue
* @param array/string files - string name of a file or array containing multiple string of files
* @param bool immediate - true to immediately process and print the file, false to merge with others
* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
* @return string - when $immediate=true the tag will be printed, "" otherwise
*/
public function js($files, $immediate=false, $how='link')

/**
* Processes/minify/combines queued files of the requested type.
* @param string type - 'js' or 'css'. This should be the end result type
* @param string how - 'link' for <script src="">, 'async' for <script src="" async>, 'embed' for <script>...js code...</script>
* @param array files - string name of a file or array containing multiple string of files
* @return string - the <script> or <link>
*/
$this->Shrink->fetch($type, $how='link')
```

Lets say you have a layout, and a view in the users controller.  The view might have at the top:

```php
<?php
	// View/Users/edit.ctp
	$this->Shrink->css(['users/edit.less']);
	$this->Shrink->js(['users/edit.coffee']);
?>
```

Then in the layout you have:
```php
<?php
	// View/Layouts/default.ctp
	$this->Shrink->css(['bootstrap.css', 'site.less']);
	echo $this->Shrink->fetch('css');

	$this->Shrink->js(['jquery.js', 'site.coffee']);
	echo $this->Shrink->fetch('js');
?>
```

Shrink will see that there are views passing js and css as well as layouts
and merge them together appropriately (layouts css and js, then views css and js)

Once fetch is called that type's queue is cleared and you can queue again.

Options
-------

Options are passed just like other helpers.

```php
// in your AppController.php
$helpers = array('Form','Html',
	'Shrink.Shrink'=>array(
		'url'=>'http://assets.trentrichardson.com'
	)
);
```

The available options to pass are currently:

```php
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
```

Extending
---------

Extending Shrink is extremely simple, and can likely be done with only a few lines of
code.  There are two abstractions, Compilers and Compressors.

Compilers are for file types like Coffee, Less, etc.  These can be found in the
app/Plugin/Shrink/src/Lib/ShrinkCompiler folder. The file and class name goes
by the file extension.  Less files have a .less extension, so will create
ShrinkCompilerLess.php.  Each compiler must set the $resultType variable to 'js' or
'css' (the end result type), and implement the compile method.

Compressors are for minifying the code after it has been compiled to js or css. These
can be found in app/Plugin/Shrink/src/Lib/ShrinkCompressor.  The file and class
name goes by the option you set in settings (the minifier option for js or css).  So
if you pass the minifier option for css to be cssmin, the file name will be
ShrinkCompressorCssmin.php.  Each compressor class must implement the compress method.

The easiest way to extend a compiler or compressor is to simply copy one of the existing
versions.  You will also notice compilers and compressors extend ShrinkBase.  This is a
utility class which currently provides a "cmd" method to execute commands.

Testing
-------

If you are making changes it is a good idea to create tests for your changes and ensure 
they are in a passing state.  Tests are run via phpunit.  Since cakephp-shrink provides 
support for command line utilities and composer packages you must install them before 
tests can run.

```sh
composer install
npm install -g less
npm install -g coffee-script
npm install -g typescript
gem install sass
```

Depending on your setup you may need to use sudo.  To run tests simply call `phpunit` 
or `vendor/bin/phpunit`.

License
-------

Copyright 2015 Trent Richardson

You may use this project under MIT or GPL licenses.

http://trentrichardson.com/Impromptu/GPL-LICENSE.txt

http://trentrichardson.com/Impromptu/MIT-LICENSE.txt
