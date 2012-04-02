<?php

namespace unittest\jamwork\form;

use jamwork\form\Checkbox;

class CheckboxTest extends \PHPUnit_Framework_TestCase
{
	private $checkbox = false;
	
	public function testType()
	{
		$type = $this->checkbox->getType();
		$this->assertSame('checkbox', $type);
	}
	
	public function testChecked()
	{
		$attrfalse = $this->readAttribute($this->checkbox, 'checked');
		$getfalse = $this->checkbox->isChecked();	
		$this->checkbox->checked();
		$attrtrue = $this->readAttribute($this->checkbox, 'checked');
		$gettrue = $this->checkbox->isChecked();
			
		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertSame($attrfalse, $getfalse);
		$this->assertSame($attrtrue, $gettrue);
	}
	
	public function testIsChecked()
	{
		// wird in testChecked getestet 
	}
	
	public function testGetFieldType()
	{
		$this->assertSame('checkbox', $this->checkbox->getFieldType());
	}
	
	protected function setUp()
	{
		$this->checkbox = new Checkbox();
	}
	
	protected function tearDown()
	{
		unset($this->checkbox);
	} 
	
}
	