<?php
namespace Shrink\Test\TestCase\Lib;

use Cake\Filesystem\File;
use Cake\TestSuite\TestCase;
use Shrink\Lib\ShrinkType;

class ShrinkCompressorTest extends TestCase
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
	* test that compressor "None" works
	*
	* @return void
	*/
	public function testCompressorNone(){
		$comp = ShrinkType::getCompressor('none', []);

		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface', $comp);

		// run against css
		$file = new File(WWW_ROOT .'css/base.css');
		$code = $file->read();
		$file->close();
		$result = $comp->compress($code);
		$expect = $code;
		$this->assertEquals($result, $expect);

		// run against js
		$file = new File(WWW_ROOT .'js/base.js');
		$code = $file->read();
		$file->close();
		$result = $comp->compress($code);
		$expect = $code;
		$this->assertEquals($result, $expect);
	}


	/**
	* test that compressor "Cssmin" works
	*
	* @return void
	*/
	public function testCompressorCssmin(){
		$comp = ShrinkType::getCompressor('cssmin', []);

		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface', $comp);

		if($comp->isAvailable()){
			// actual result
			$file = new File(WWW_ROOT .'css/base.css');
			$code = $file->read();
			$result = $comp->compress($code);
			$file->close();

			// expected result
			$minfile = new File(WWW_ROOT .'css/base.cssmin.css');
			$expect = $minfile->read();
			$minfile->close();
			$this->assertEquals($result, $expect, 'Compressed css does not match. Ensure CssMin package is available via composer.');
		}
		else{
			echo "\nSkipping cssmin tests, no CssMin available via composer.\n";
		}
	}


	/**
	* test that compressor "Jshrink" works
	*
	* @return void
	*/
	public function testCompressorJshrink(){
		$comp = ShrinkType::getCompressor('jshrink', []);

		// verify the instance
		$this->assertInstanceOf('\Shrink\Lib\ShrinkCompressor\ShrinkCompressorInterface', $comp);

		if($comp->isAvailable()){
			// actual result
			$file = new File(WWW_ROOT .'js/base.js');
			$code = $file->read();
			$result = $comp->compress($code);
			$file->close();

			// expected result
			$minfile = new File(WWW_ROOT .'js/base.jshrink.js');
			$expect = $minfile->read();
			$minfile->close();

			$this->assertEquals($result, $expect, 'Compressed js does not match. Ensure jshrink package is available via composer.');
		}
		else{
			echo "\nSkipping Jshrink tests, no Jshrink available via composer.\n";
		}
	}
}
