<?php
namespace Shrink\Test\TestCase\View\Helper;

use Shrink\View\Helper\ShrinkHelper;
use Cake\View\View;
use Cake\Filesystem\File;
use Cake\TestSuite\TestCase;

class ShrinkHelperTest extends TestCase
{

	/**
	* setup any prelimiary structure before a test
	*
	* @return void
	*/
	public function setUp() {
		parent::setUp();

		$view = new View(null);
		$this->Shrink = new ShrinkHelper($view, []);
	}


	/**
	* do any break down after a test
	*
	* @return void
	*/
	public function tearDown() {
		parent::tearDown();

		unset($this->Shrink);
	}


	/**
	* test that options are properly set
	*
	* @return void
	*/
	public function testShrinkCss(){
		$this->Shrink->css([
				'base.css',
				'base.less',
				'base.scss'
			]);
		$tag = $this->Shrink->fetch('css');

		// did it create a link tag?
		$this->assertRegExp('/^\<link\s/', $tag);

		// grab the url if it has one (it always should)
		preg_match('/href="(?P<url>.+?)"/', $tag, $matches);
		$this->assertArrayHasKey('url', $matches);

		// verify the file exists
		$url = $matches['url'];
		$file = new File(WWW_ROOT . $url);
		$this->assertTrue($file->exists());

		// verify the contents
		$result = $file->read();
		$file->delete();
		unset($file);

		$expectedfile = new File(WWW_ROOT .'css/base.shrink.css');
		$expect = $expectedfile->read();
		unset($expectedfile);

		$this->assertEquals($result, $expect);
	}


	/**
	* test that options are properly set
	*
	* @return void
	*/
	public function testShrinkJs(){
		$this->Shrink->js([
				'base.js',
				'base.coffee'
			]);
		$tag = $this->Shrink->fetch('js');

		// did it create a link tag?
		$this->assertRegExp('/^\<script\s/', $tag);

		// grab the url if it has one (it always should)
		preg_match('/src="(?P<url>.+?)"/', $tag, $matches);
		$this->assertArrayHasKey('url', $matches);

		// verify the file exists
		$url = $matches['url'];
		$file = new File(WWW_ROOT . $url);
		$this->assertTrue($file->exists());

		// verify the contents
		$result = $file->read();
		$file->delete();
		unset($file);

		$expectedfile = new File(WWW_ROOT .'js/base.shrink.js');
		$expect = $expectedfile->read();
		unset($expectedfile);

		$this->assertEquals($result, $expect);
	}
	
}