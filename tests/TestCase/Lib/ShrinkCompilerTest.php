<?php
namespace Shrink\Test\TestCase\Lib;

use Cake\Filesystem\File;
use Cake\TestSuite\TestCase;
use Shrink\Lib\ShrinkType;

class ShrinkCompilerTest extends TestCase
{

	/**
	* setup any prelimiary structure before a test
	*
	* @return void
	*/
	public function setUp() {
		parent::setUp();

	}


	/**
	* do any break down after a test
	*
	* @return void
	*/
	public function tearDown() {
		parent::tearDown();

	}


	/**
	* test that compiler "css" works
	*
	* @return void
	*/
	public function testCompilerCss(){
		$compiler = ShrinkType::getCompiler('css', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		// run against css
		$file = new File(WWW_ROOT .'css/base.css');
		$result = $compiler->compile($file);
		$expect = $file->read();
		$file->close();

		$this->assertEquals($expect, $result);
	}


	/**
	* test that compiler "js" works
	*
	* @return void
	*/
	public function testCompilerJs(){
		$compiler = ShrinkType::getCompiler('js', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		// run against js
		$file = new File(WWW_ROOT .'js/base.js');
		$result = $compiler->compile($file);
		$expect = $file->read();
		$file->close();

		$this->assertEquals($expect, $result);
	}


	/**
	* test that compiler "less" works
	*
	* @return void
	*/
	public function testCompilerLess(){
		$compiler = ShrinkType::getCompiler('less', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'css/base.less');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$cssfile = new File(WWW_ROOT .'css/base.less.css');
			$expect = $cssfile->read();
			$cssfile->close();

			$this->assertEquals($expect, $result, 'Compiled less does not match. Ensure lessphp package is available via composer.');
		}
		else{
			echo "\nSkipping less tests, no lessphp available via composer.\n";
		}
	}


	/**
	* test that compiler "less" works
	*
	* @return void
	*/
	public function testCompilerLessCmd(){
		$compiler = ShrinkType::getCompiler('less', ['less'=>['less'=>'lessc']]);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'css/base.less');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$cssfile = new File(WWW_ROOT .'css/base.lesscmd.css');
			$expect = $cssfile->read();
			$cssfile->close();

			$this->assertEquals($expect, $result, 'Compiled less does not match. Ensure lessc command line utility is available. npm install -g less');
		}
		else{
			echo "\nSkipping less tests, no lessc available via composer.\n";
		}
	}


	/**
	* test that compiler "scss" works
	*
	* @return void
	*/
	public function testCompilerScss(){
		$compiler = ShrinkType::getCompiler('scss', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'css/base.scss');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$cssfile = new File(WWW_ROOT .'css/base.scss.css');
			$expect = $cssfile->read();
			$cssfile->close();

			$this->assertEquals($expect, $result, 'Compiled scss does not match. Ensure sassphp package is available via composer.');
		}
		else{
			echo "\nSkipping Scss tests, no sassphp available via composer.\n";
		}
	}


	/**
	* test that compiler "scss" works with command line version
	*
	* @return void
	*/
	public function testCompilerScssCmd(){
		$compiler = ShrinkType::getCompiler('scss', ['sass'=>['sass'=>'sass']]);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'css/base.scss');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$cssfile = new File(WWW_ROOT .'css/base.scsscmd.css');
			$expect = $cssfile->read();
			$cssfile->close();

			$this->assertEquals($expect, $result, 'Compiled scss does not match. Ensure Sass command line utility is available. gem install sass');
		}
		else{
			echo "\nSkipping Scss tests, no scss available: gem install sass\n";
		}
	}


	/**
	* test that compiler "scss" works
	*
	* @return void
	*/
	public function testCompilerSass(){
		$compiler = ShrinkType::getCompiler('sass', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'css/base.sass');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$cssfile = new File(WWW_ROOT .'css/base.sass.css');
			$expect = $cssfile->read();
			$cssfile->close();

			$this->assertEquals($expect, $result, 'Compiled sass does not match. Ensure Sass command line utility is available. gem install sass');
		}
		else{
			echo "\nSkipping Sass tests, no sass available: gem install sass\n";
		}
	}


	/**
	* test that compiler "coffee" works
	*
	* @return void
	*/
	public function testCompilerCoffee(){
		$compiler = ShrinkType::getCompiler('coffee', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'js/base.coffee');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$jsfile = new File(WWW_ROOT .'js/base.coffee.js');
			$expect = $jsfile->read();
			$jsfile->close();

			$this->assertEquals($expect, $result, 'Compiled coffee does not match. Ensure Coffee script command line utility is available. npm install -g coffee-script');
		}
		else{
			echo "\nSkipping Coffee Script tests, no coffee available: npm install -g coffee-script\n";
		}
	}


	/**
	* test that compiler "ts" works
	*
	* @return void
	*/
	public function testCompilerTypescript(){
		$compiler = ShrinkType::getCompiler('ts', []);
		
		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompiler\ShrinkCompilerInterface', $compiler);

		if($compiler->isAvailable()){
			// get the result
			$file = new File(WWW_ROOT .'js/base.ts');
			$result = $compiler->compile($file);
			$file->close();

			// get the expected result
			$jsfile = new File(WWW_ROOT .'js/base.ts.js');
			$expect = $jsfile->read();
			$jsfile->close();

			$this->assertEquals($expect, $result, 'Compiled Typescript does not match. Ensure Typescript script command line utility is available. npm install -g typescript');
		}
		else{
			echo "\nSkipping Typescript tests, no typescript available: npm install -g typescript\n";
		}
	}
}