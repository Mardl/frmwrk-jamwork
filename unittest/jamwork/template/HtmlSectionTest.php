<?php

namespace unittest\jamwork\template;

use \jamwork\template\HtmlSection;

class HtmlSectionTest extends \PHPUnit_Framework_TestCase
{
	private $htmlSection = False;
	
	public function testAppend()
	{
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertEmpty($attr);
		$this->htmlSection->append('foo');
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertSame('foo', $attr);
		$this->htmlSection->append('oof');
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertSame('foooof', $attr);
		
	}
	
	public function testPrepend()
	{
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertEmpty($attr);
		$this->htmlSection->append('foo');
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertSame('foo', $attr);
		$this->htmlSection->prepend('oof');
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertSame('ooffoo', $attr);	
	}
	
	public function testFlush()
	{
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertEmpty($attr);
		$this->htmlSection->append('foo');
		$attr = $this->readAttribute($this->htmlSection, 'data');
		$this->assertSame('foo', $attr);
		$flush = $this->htmlSection->flush();
		$this->assertSame('foo', $flush);
	}
	
	protected function setUp()
	{
		$this->htmlSection = new HtmlSection();
	}
	
	protected function tearDown()
	{
		unset($this->htmlSection);
	}
}
