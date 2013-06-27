<?php

namespace unittest\jamwork\form;

use jamwork\form\Textfield;

class TextfieldTest extends \PHPUnit_Framework_TestCase
{

	public function testType()
	{
		$type = $this->textfield->getType();
		$this->assertSame('text', $type);
	}

	public function testMaxLength()
	{
		$attrempty = $this->readAttribute($this->textfield, 'maxLength');
		$getempty = $this->textfield->getMaxLength();
		$this->textfield->maxLength('maxLength');
		$attrfilled = $this->readAttribute($this->textfield, 'maxLength');
		$getfilled = $this->textfield->getMaxLength();

		$this->assertEmpty($attrempty);
		$this->assertSame('maxLength', $attrfilled);
		$this->assertsame($attrempty, $getempty);
		$this->assertsame($attrfilled, $getfilled);
	}

	public function testGetMaxLength()
	{
		// wird indirekt getestet
	}

	public function testGetFieldType()
	{
		$this->assertSame('text', $this->textfield->getFieldType());
	}

	protected function setUp()
	{
		$this->textfield = new Textfield();
	}

	protected function tearDown()
	{
		unset($this->textfield);
	}


}