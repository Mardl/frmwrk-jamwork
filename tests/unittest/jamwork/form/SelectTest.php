<?php

namespace unittest\jamwork\form;

use jamwork\form\Select;

class SelectTest extends \PHPUnit_Framework_TestCase
{

	public function testMultiple()
	{
		$attrfalse = $this->readAttribute($this->select, 'multiple');
		$getfalse = $this->select->isMultiple();
		$this->select->multiple();
		$attrtrue = $this->readAttribute($this->select, 'multiple');
		$gettrue = $this->select->isMultiple();

		$this->assertFalse($attrfalse);
		$this->assertTrue($attrtrue);
		$this->assertSame($attrfalse, $getfalse);
		$this->assertSame($attrtrue, $gettrue);
	}

	public function testIsMultiple()
	{
		// wird indirekt getestet
	}

	public function testType()
	{
		$type = $this->select->getType();
		$this->assertSame('select', $type);
	}

	public function testNewOption_instance()
	{
		$option = $this->select->newOption();
		$this->assertInstanceOf('\jamwork\form\Option', $option);
	}

	public function testNewOption_value()
	{
		$value = $this->readAttribute($this->select, 'value');
		$this->assertEmpty($value);

		$this->select->newOption();
		$this->select->newOption();

		$value = $this->readAttribute($this->select, 'value');
		$this->assertTrue(is_array($value));
		$this->assertSame(count($value), 2);

		foreach ($value as $val)
		{
			$this->assertInstanceOf('\jamwork\form\Option', $val);
		}
	}

	public function testGetFieldType()
	{
		$this->assertSame('select', $this->select->getFieldType());
	}

	protected function setUp()
	{
		$this->select = new Select();
	}

	protected function tearDown()
	{
		unset($this->select);
	}


}