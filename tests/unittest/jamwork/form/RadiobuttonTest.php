<?php

namespace unittest\jamwork\form;

use jamwork\form\Radiobutton;

class RadiobuttonTest extends \PHPUnit_Framework_TestCase
{

	private $checkbox = false;

	public function testType()
	{
		$type = $this->radio->getType();
		$this->assertSame('radio', $type);
	}

	protected function setUp()
	{
		$this->radio = new radiobutton();
	}

	public function testGetFieldType()
	{
		$this->assertSame('radio', $this->radio->getFieldType());
	}

	protected function tearDown()
	{
		unset($this->radio);
	}

}