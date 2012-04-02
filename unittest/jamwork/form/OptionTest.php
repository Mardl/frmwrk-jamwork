<?php

namespace unittest\jamwork\form;

use jamwork\form\Option;

class OptionTest extends \PHPUnit_Framework_TestCase
{
	public function testText()
	{
		$attrfalse = $this->readAttribute($this->option, 'text');
		$getfalse = $this->option->getText();	
		$this->option->text('text');
		$attrtrue = $this->readAttribute($this->option, 'text');
		$gettrue = $this->option->getText();
			
		$this->assertSame('', $attrfalse);
		$this->assertSame('text', $attrtrue);
		$this->assertSame($attrfalse, $getfalse);
		$this->assertSame($attrtrue, $gettrue);
	}
	
	public function testGetText()
	{
		// wird indirekt getestet
	}
	
	public function testValue()
	{
		$attrfalse = $this->readAttribute($this->option, 'value');
		$getfalse = $this->option->getValue();	
		$this->option->value('value');
		$attrtrue = $this->readAttribute($this->option, 'value');
		$gettrue = $this->option->getValue();
			
		$this->assertSame('', $attrfalse);
		$this->assertSame('value', $attrtrue);
		$this->assertSame($attrfalse, $getfalse);
		$this->assertSame($attrtrue, $gettrue);
	}
	
	public function testGetValue()
	{
		// wird indirekt getestet
	}
	
	public function testSelected()
	{
		$attrfalse = $this->readAttribute($this->option, 'selected');
		$getfalse = $this->option->isSelected();	
		$this->option->Selected();
		$attrtrue = $this->readAttribute($this->option, 'selected');
		$gettrue = $this->option->isSelected();
			
		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertSame($attrfalse, $getfalse);
		$this->assertSame($attrtrue, $gettrue);
	}
	
	public function testIsSelected()
	{
		// wird indirekt getestet
	}
	
	public function testGetFieldType()
	{
		$this->assertSame('option', $this->option->getFieldType());
	}
	
	protected function setUp()
	{
		$this->option = new Option();
	}
	
	protected function tearDown()
	{
		unset($this->option);
	} 
	
}